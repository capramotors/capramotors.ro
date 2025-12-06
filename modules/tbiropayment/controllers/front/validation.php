<?php
    /**
        * @File: validation.php
        * @Author: Ilko Ivanov
        * @Author e-mail: ilko.iv@gmail.com
        * @Publisher: Avalon Ltd
        * @Publisher e-mail: home@avalonbg.com
        * @Owner: Avalon Ltd
        * @Version: 1.0.1
    */
/**
 * @since 1.5.0
 */
class TbiropaymentValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $tbiro_type = Tools::getValue('tbiro_type');
        
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');
        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = true;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'tbiropayment')
            {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die($this->trans('This payment method is not available.', [], 'Modules.Tbiropayment.Front'));
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $mailVars = array(
            '{bankwire_owner}' => 'owner',
            '{bankwire_details}' => nl2br('details'),
            '{bankwire_address}' => nl2br('address')
        );
        
        $tbiro_products = $cart->getProducts(true);
        $tbiro_products_price = $cart->getordertotal(true);
        $tbiro_unicid = (string)Configuration::get('CREDITTBIRO_UNICID');
        $tbiro_store_id = (string)Configuration::get('CREDITTBIRO_STORE_ID');
        $tbiro_username = (string)Configuration::get('CREDITTBIRO_USERNAME');
        $tbiro_password = (string)Configuration::get('CREDITTBIRO_PASSWORD');
        $tbiro_iris_iban = (string)Configuration::get('CREDITTBIRO_IRIS_IBAN');
        
        $tbiro_ch = curl_init();
        curl_setopt($tbiro_ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($tbiro_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tbiro_ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($tbiro_ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($tbiro_ch, CURLOPT_URL, TBIRO_LIVEURL . '/function/getparameters.php?cid='.$tbiro_unicid);
        $paramstbiro=json_decode(curl_exec($tbiro_ch), true);
        curl_close($tbiro_ch);
        
        $tbiro_products_id = '';
        $tbiro_products_q = '';
        $tbiro_products_n = '';
            
        $ident = 0;
        $tbiro_items = array();
        $tbiro_api_items = array();
        foreach ($tbiro_products as $tbiro_product){
            $tbiro_irems[$ident]['name'] = preg_replace("/[^A-Za-z0-9?! ]/","",$tbiro_product['name']);
            $tbiro_api_items[$ident]['name'] = preg_replace("/[^A-Za-z0-9?! ]/","",$tbiro_product['name']);
            $tbiro_api_items[$ident]['description'] = '';
            $tbiro_quantity = $tbiro_product['quantity'];
            $tbiro_irems[$ident]['qty'] = "$tbiro_quantity";
            $tbiro_api_items[$ident]['qty'] = "$tbiro_quantity";
            $tbiro_price = $tbiro_product['price_wt'];
            $tbiro_irems[$ident]['price'] = "$tbiro_price";
            $tbiro_api_items[$ident]['price'] = "$tbiro_price";
            $tbiro_category = $tbiro_product['id_category_default'];
            $tbiro_irems[$ident]['category'] = "$tbiro_category";
            $tbiro_api_items[$ident]['category'] = "$tbiro_category";
            $tbiro_product_id = $tbiro_product['id_product'];
            $tbiro_irems[$ident]['sku'] = "$tbiro_product_id";
            $tbiro_image = Image::getCover($tbiro_product['id_product']);
            $tbiro_link = new Link;
            $tbiro_imagePath = $tbiro_link->getImageLink($tbiro_product['link_rewrite'], $tbiro_image['id_image'], 'home_default');
            $tbiro_items[$ident]['ImageLink'] = "$tbiro_imagePath";
            $tbiro_api_items[$ident]['ImageLink'] = "$tbiro_imagePath";
            $ident++;
        }
        
        $this->module->validateOrder($cart->id, Configuration::get('PS_OS_TBIROPAYMENT'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
        $order_conf_link = $this->context->link->getPageLink('order-confirmation');
        if (strpos($order_conf_link, '?') !== FALSE) {
            $url_separator = '&';
        } else {
            $url_separator = '?';
        }
            
        $tbiro_url = TBIRO_LIVEURL . "/function/status.php";
        $order_id = $this->module->currentOrder;
        $tbiro_address_delivery_id = isset($this->context->cart->id_address_delivery) ? $this->context->cart->id_address_delivery : '';
        $tbiro_address_invoice_id = isset($this->context->cart->id_address_invoice) ? $this->context->cart->id_address_invoice : '';
        $tbiro_addresses = $this->context->customer->getAddresses($this->context->customer->id_lang);
        foreach ($tbiro_addresses as $tbiro_address){
            if ($tbiro_address['id_address'] == $tbiro_address_delivery_id){
                $tbiro_shipping_addresses = $tbiro_address;
            }
            if ($tbiro_address['id_address'] == $tbiro_address_invoice_id){
                $tbiro_billing_addresses = $tbiro_address;
            }
        }
            
        $tbiro_fname = isset($this->context->customer->firstname) ? $this->context->customer->firstname : '';
        $tbiro_lname = isset($this->context->customer->lastname) ? $this->context->customer->lastname : '';
        $tbiro_cnp = '';
        $tbiro_email = isset($this->context->customer->email) ? $this->context->customer->email : '';
        $tbiro_phone = isset($tbiro_shipping_addresses['phone']) ? $tbiro_shipping_addresses['phone'] : '';
        $tbiro_billing_address = isset($tbiro_billing_addresses['address1']) ? $tbiro_billing_addresses['address1'] : '';
        $tbiro_billing_city = isset($tbiro_billing_addresses['city']) ? $tbiro_billing_addresses['city'] : '';
        $tbiro_billing_county = isset($tbiro_billing_addresses['state']) ? $tbiro_billing_addresses['state'] : '';
        $tbiro_shipping_address = isset($tbiro_shipping_addresses['address1']) ? $tbiro_shipping_addresses['address1'] : '';
        $tbiro_shipping_city = isset($tbiro_shipping_addresses['city']) ? $tbiro_shipping_addresses['city'] : '';
        $tbiro_shipping_county = isset($tbiro_shipping_addresses['state']) ? $tbiro_shipping_addresses['state'] : '';
        $tbiro_person_type = '';
        $tbiro_net_income = '';
        $tbiro_instalments = '';
            
        // Create tbiro order i data base
        $tbiro_add_ch = curl_init();
        curl_setopt($tbiro_add_ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($tbiro_add_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tbiro_add_ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($tbiro_add_ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($tbiro_add_ch, CURLOPT_URL, TBIRO_LIVEURL . '/function/addorders.php?cid='.$tbiro_unicid);
        curl_setopt($tbiro_add_ch, CURLOPT_POST, 1);
            
        $tbiro_post = array(
            'store_id'        =>    $tbiro_store_id,// store id
            'order_id'        =>    $order_id,// id order
            'type'          =>  strtoupper($tbiro_type),
            'back_ref'        =>    $tbiro_url,// back to this after populate form
            'order_total'    =>    $tbiro_products_price,// product price
            'username'        =>    $tbiro_username,
            'password'        =>    $tbiro_password,
            'customer'        =>    array(
                'fname'                =>    $tbiro_fname,//name
                'lname'                =>    $tbiro_lname,//famyly
                'cnp'                =>    $tbiro_cnp,//cnp,
                'email'                =>    $tbiro_email,//email,
                'phone'                =>    $tbiro_phone,//phone,
                'billing_address'    =>    $tbiro_billing_address,//address invoice,
                'billing_city'        =>    $tbiro_billing_city,//city,
                'billing_county'    =>    $tbiro_billing_county,//country,
                'shipping_address'    =>    $tbiro_shipping_address,//shipping address,
                'shipping_city'        =>    $tbiro_shipping_city,//shipping sity,
                'shipping_county'    =>    $tbiro_shipping_county,//shipping country,
                'person_type'        =>    '',// 'Angajat', 'Pensionar', 'PFA', 'Altele'
                'net_income'        =>    '',// net income
                'instalments'        =>    '',
            ),
            'items'            =>    $tbiro_irems
        );
            
        curl_setopt($tbiro_add_ch, CURLOPT_POSTFIELDS, http_build_query($tbiro_post));
        $paramstbiroadd=json_decode(curl_exec($tbiro_add_ch), true);
        curl_close($tbiro_add_ch);
        
        // Create tbiro order i data base
        if (isset($paramstbiroadd['status']) && ($paramstbiroadd['status'] == 'Yes')){
            // save to tbiorders file
            $tbiro_tempcontent = file_get_contents(_PS_MODULE_DIR_ . 'tbiropayment/keys/tbiroorders.json');
            if ($tbiro_tempcontent != false){
                $tbiro_orders = json_decode($tbiro_tempcontent);
                // test over 1000
                if (is_array($tbiro_orders) && (count($tbiro_orders) >= 1000)){
                    array_shift($tbiro_orders);
                }
                $tbiro_order_current = array(
                    "order_id" => $order_id,
                    "order_status" => "Draft"
                );
                $key = array_search($order_id, array_map(
                    function($o) {
                        return $o->order_id;
                    }, 
                    $tbiro_orders));
                if ($key === false){
                    array_push($tbiro_orders, $tbiro_order_current);
                }
                $jsondata = json_encode($tbiro_orders);
                file_put_contents(_PS_MODULE_DIR_ . 'tbiropayment/keys/tbiroorders.json', $jsondata);
            }
                
            // send to softinteligens
            $tbiro_post_arr = array(
                'store_id'        =>    $tbiro_store_id,// store id
                'order_id'        =>    $paramstbiroadd['newid'],// id order
                'back_ref'        =>    $tbiro_url,// back to this after populate form
                'order_total'    =>    $tbiro_products_price,// product price
                'username'        =>    $tbiro_username,
                'password'        =>    $tbiro_password,
                'avalonorderid'    => (string)$order_id,
                'customer'        =>    array(
                    'fname'                =>    $tbiro_fname,//name
                    'lname'                =>    $tbiro_lname,//famyly
                    'cnp'                =>    $tbiro_cnp,//cnp,
                    'email'                =>    $tbiro_email,//email,
                    'phone'                =>    $tbiro_phone,//phone,
                    'billing_address'    =>    $tbiro_billing_address,//address invoice,
                    'billing_city'        =>    $tbiro_billing_city,//city,
                    'billing_county'    =>    $tbiro_billing_county,//country,
                    'shipping_address'    =>    $tbiro_shipping_address,//shipping address,
                    'shipping_city'        =>    $tbiro_shipping_city,//shipping sity,
                    'shipping_county'    =>    $tbiro_shipping_county,//shipping country,
                    'person_type'        =>    '',// 'Angajat', 'Pensionar', 'PFA', 'Altele'
                    'net_income'        =>    '',// net income
                    'instalments'        =>    '',
                ),
                'items'            =>    $tbiro_irems
            );
            
            // send to IRIS
            $tbigriris_post_arr = array(
                'currency'          => 'RON',
                'description'          => 'TBI IRIS Payment',
                'hookUrl'              => $tbiro_url . '?order=' . $paramstbiroadd['newid'],
                'name'               => '',
                'redirectUrl'        => Tools::getShopDomainSsl(true, true).'/index.php?controller=order',
                'sum'                => $tbiro_products_price,
                'toIban'             => $tbiro_iris_iban,
                'orderId'            => $paramstbiroadd['newid'] . "," . $order_id
            );
            
            $tbiro_post = $tbiro_post_arr;
            if ($paramstbiro['tbi_testenv'] == 1){
                $tbiro_envurl = $paramstbiro['tbi_testurl'];
                $tbiro_env = 'tbi_test';
            }else{
                $tbiro_envurl = $paramstbiro['tbi_liveurl'];
                $tbiro_env = 'tbi_live';
            }
            if ($tbiro_type == 'IRIS'){
                $tbiro_post = $tbigriris_post_arr;
                if ($paramstbiro['iris_testenv'] == 1){
                    $tbiro_envurl = $paramstbiro['iris_testurl'];
                    $tbiro_env = 'iris_test';
                }else{
                    $tbiro_envurl = $paramstbiro['iris_liveurl'];
                    $tbiro_env = 'iris_live';
                }
            }
            
            $tbi_pause_txt = $paramstbiro['tbi_pause_txt'];
        }
        
        $cookie = new Cookie('tbiropayment_cookie');
        $cookie->setExpire(time() + 20 * 60);
        $cookie->send_tbiro = 'No';
        $cookie->write();
        
        Tools::redirect($order_conf_link.$url_separator.'controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key.'&enc='.urlencode(base64_encode(json_encode($tbiro_post, JSON_UNESCAPED_UNICODE))).'&envurl='.$tbiro_envurl.'&tbi_pause_txt='.$tbi_pause_txt.'&tbiro_type='.$tbiro_type.'&tbiro_env='.$tbiro_env);
    }
}
