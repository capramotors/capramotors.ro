<?php

/**
 * @File: tbiropayment.php
 * @Author: Ilko Ivanov
 * @Author e-mail: ilko.iv@gmail.com
 * @Publisher: Avalon Ltd
 * @Publisher e-mail: home@avalonbg.com
 * @Owner: Avalon Ltd
 * @Version: 3.4.2
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

if (!defined('TBIRO_LIVEURL'))
    define('TBIRO_LIVEURL', 'https://tbicp.com');

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class TbiroPayment extends PaymentModule {

    const HOOKS = [
        'ActionFrontControllerSetMedia',
        'displayProductAdditionalInfo',
        'paymentOptions',
        'displayHome',
        'paymentReturn'
    ];

    public function __construct() {
        $this->name = 'tbiropayment';
        $this->tab = 'payments_gateways';
        $this->version = '3.4.2';
        $this->author = 'Ilko Ivanov';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('tbi bank RO', [], 'Modules.Tbiropayment.Admin');
        $this->description = $this->trans('Credit calculator', [], 'Modules.Tbiropayment.Admin');

        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '9.99.99'];

        $this->confirmUninstall = $this->trans('Are you sure that you want to uninstall?', [], 'Modules.Tbiropayment.Admin');

        if (!Configuration::get('TBIROPAYMENT_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.Tbiropayment.Admin');
        }
    }

    public function install() {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);
        if (!(Configuration::get('PS_OS_TBIROPAYMENT') > 0)){
            $tbiro_name = "tbi bank Payment awaiting validation";
            $tbiro_state_exist = false;
            $tbiro_state_id = 0;
            $tbiro_states = OrderState::getOrderStates((int)$this->context->language->id);
            foreach ($tbiro_states as $state) {
                if (in_array($tbiro_name, $state)) {
                    $tbiro_state_exist = true;
                    $tbiro_state_id = $state['id_order_state'];
                    break;
                }
            }
            if (!$tbiro_state_exist){
                $tbiropayment_OrderState = new OrderState();
                $tbiropayment_OrderState->color = "#DDEAF8";
                $tbiropayment_OrderState->send_mail = 0;
                $tbiropayment_OrderState->name = $this->name;
                $tbiropayment_OrderState->template = "";
                $tbiropayment_OrderState->invoice = 0;
                $tbiropayment_OrderState->logable = 0;
                $tbiropayment_OrderState->unremovable = false;
                $tbiropayment_OrderState->name = array();
                $tbiro_languages = Language::getLanguages(false);
                foreach ($tbiro_languages as $language)
                    $tbiropayment_OrderState->name[$language['id_lang']] = $tbiro_name;
                $tbiropayment_OrderState->add();
                Configuration::updateValue('PS_OS_TBIROPAYMENT',$tbiropayment_OrderState->id);
            }else{
                Configuration::updateValue('PS_OS_TBIROPAYMENT',$tbiro_state_id);
            }
        }
        return parent::install() &&
            (bool) $this->registerHook(static::HOOKS) &&
            Configuration::updateValue('TBIROPAYMENT_NAME', 'tbi pay ro payment');
    }

    public function uninstall() {
        if (
          !parent::uninstall() ||
          !Configuration::deleteByName('TBIROPAYMENT_NAME') ||
          !Configuration::deleteByName('PS_OS_TBIROPAYMENT') ||
          !Configuration::deleteByName('CREDITTBIRO_STATUS') ||
          !Configuration::deleteByName('CREDITTBIRO_SHOW_STATUS') ||
          !Configuration::deleteByName('CREDITTBIRO_UNICID') ||
          !Configuration::deleteByName('CREDITTBIRO_STORE_ID') ||
          !Configuration::deleteByName('CREDITTBIRO_USERNAME') ||
          !Configuration::deleteByName('CREDITTBIRO_PASSWORD') ||
          !Configuration::deleteByName('CREDITTBIRO_FIRSTLABEL') ||
          !Configuration::deleteByName('CREDITTBIRO_SECONDLABEL') ||
          !Configuration::deleteByName('CREDITIRISRO_FIRSTLABEL') ||
          !Configuration::deleteByName('CREDITIRISRO_SECONDLABEL') ||
          !Configuration::deleteByName('CREDITTBIRO_IRIS_IBAN') ||
          !Configuration::deleteByName('CREDITTBIRO_IRIS_KEY')
        )
          return false;
        return true;
    }

    public function isUsingNewTranslationSystem() {
        return true;
    }

    public function getContent() {
        $route = $this->get('router')->generate('tbiro_payment_configuration_form');
        Tools::redirectAdmin($route);
    }

    public function hookActionFrontControllerSetMedia($params) {
        if ('index' === $this->context->controller->php_self) {
            $this->context->controller->registerStylesheet(
                'tbiropanel-home-page',
                'modules/'.$this->name.'/css/tbiropanel.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                ]
            );
        }
    }

    public function tbiro_PMT($rate, $nper, $pv, $fv=0, $type = 0) {
        return (-$fv - $pv * pow(1 + $rate, $nper)) / (1 + $rate * $type) / ((pow(1 + $rate, $nper) - 1) / $rate);
    }

    public function hookDisplayProductAdditionalInfo($params) {
        $tbiro_show_status = intval(Configuration::get('CREDITTBIRO_SHOW_STATUS'));
        if ($tbiro_show_status === 1){
            if ('product' === $this->context->controller->php_self){
                $tbiro_products = (int)Tools::getValue('id_product');
                $tbiro_product = new Product($tbiro_products, true, $this->context->language->id);
                $tbiro_price = (float)Product::getPriceStatic($tbiro_products, true);
                $tbiro_status = (string)Configuration::get('CREDITTBIRO_STATUS');
                $tbiro_unicid = (string)Configuration::get('CREDITTBIRO_UNICID');
                $tbiro_ch = curl_init();
                curl_setopt($tbiro_ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($tbiro_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($tbiro_ch, CURLOPT_MAXREDIRS, 2);
                curl_setopt($tbiro_ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($tbiro_ch, CURLOPT_URL, TBIRO_LIVEURL . '/function/getparameters.php?cid='.$tbiro_unicid);
                $paramstbiro = json_decode(curl_exec($tbiro_ch), true);
                curl_close($tbiro_ch);
                $tbi_btn_color = '#e55a00;';
                if ($paramstbiro['tbi_btn_theme'] == 'tbi'){
                    $tbi_btn_color = '#e55a00;';
                }
                if ($paramstbiro['tbi_btn_theme'] == 'tbi2'){
                    $tbi_btn_color = '#00368a;';
                }
                if ($paramstbiro['tbi_btn_theme'] == 'tbi3'){
                    $tbi_btn_color = '#2b7953;';
                }
                if ($paramstbiro['tbi_btn_theme'] == 'tbi4'){
                    $tbi_btn_color = '#848789;';
                }

                $tbi_divider = floatval($paramstbiro['tbi_divider']);
                $tbi_divider2 = floatval($paramstbiro['tbi_divider2']);
                $tbi_divider2_is = boolval($paramstbiro['tbi_divider2_is']);
                $tbi_divider3 = floatval($paramstbiro['tbi_divider3']);
                $tbi_divider3_is = boolval($paramstbiro['tbi_divider3_is']);
                $tbi_divider4 = floatval($paramstbiro['tbi_divider4']);
                $tbi_divider4_is = boolval($paramstbiro['tbi_divider4_is']);
                $tbi_divider5 = floatval($paramstbiro['tbi_divider5']);
                $tbi_divider5_is = boolval($paramstbiro['tbi_divider5_is']);

                if ($tbi_divider5_is){
                    //divider 5
                    if ($tbiro_price < $tbi_divider){
                        $tbi_rate = floatval($paramstbiro['tbi_rate']);
                        $tbi_commission = floatval($paramstbiro['tbi_commission']);
                        $tbi_insurance = floatval($paramstbiro['tbi_insurance']);
                        $tbi_months = intval($paramstbiro['tbi_months']);
                    }else{
                        if ($tbiro_price >= $tbi_divider && $tbiro_price < $tbi_divider2){
                            $tbi_rate = floatval($paramstbiro['tbi_rate2']);
                            $tbi_commission = floatval($paramstbiro['tbi_commission2']);
                            $tbi_insurance = floatval($paramstbiro['tbi_insurance2']);
                            $tbi_months = intval($paramstbiro['tbi_months2']);
                        }else{
                            if ($tbiro_price >= $tbi_divider2 && $tbiro_price < $tbi_divider3){
                                $tbi_rate = floatval($paramstbiro['tbi_rate3']);
                                $tbi_commission = floatval($paramstbiro['tbi_commission3']);
                                $tbi_insurance = floatval($paramstbiro['tbi_insurance3']);
                                $tbi_months = intval($paramstbiro['tbi_months3']);
                            }else{
                                if ($tbiro_price >= $tbi_divider3 && $tbiro_price < $tbi_divider4){
                                    $tbi_rate = floatval($paramstbiro['tbi_rate4']);
                                    $tbi_commission = floatval($paramstbiro['tbi_commission4']);
                                    $tbi_insurance = floatval($paramstbiro['tbi_insurance4']);
                                    $tbi_months = intval($paramstbiro['tbi_months4']);
                                }else{
                                    if ($tbiro_price >= $tbi_divider4 && $tbiro_price < $tbi_divider5){
                                        $tbi_rate = floatval($paramstbiro['tbi_rate5']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission5']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance5']);
                                        $tbi_months = intval($paramstbiro['tbi_months5']);
                                    }else{
                                        $tbi_rate = floatval($paramstbiro['tbi_rate6']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission6']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance6']);
                                        $tbi_months = intval($paramstbiro['tbi_months6']);
                                    }
                                }
                            }
                        }
                    }
                }else{
                    if ($tbi_divider4_is){
                        //divider 4
                        if ($tbiro_price < $tbi_divider){
                            $tbi_rate = floatval($paramstbiro['tbi_rate']);
                            $tbi_commission = floatval($paramstbiro['tbi_commission']);
                            $tbi_insurance = floatval($paramstbiro['tbi_insurance']);
                            $tbi_months = intval($paramstbiro['tbi_months']);
                        }else{
                            if ($tbiro_price >= $tbi_divider && $tbiro_price < $tbi_divider2){
                                $tbi_rate = floatval($paramstbiro['tbi_rate2']);
                                $tbi_commission = floatval($paramstbiro['tbi_commission2']);
                                $tbi_insurance = floatval($paramstbiro['tbi_insurance2']);
                                $tbi_months = intval($paramstbiro['tbi_months2']);
                            }else{
                                if ($tbiro_price >= $tbi_divider2 && $tbiro_price < $tbi_divider3){
                                    $tbi_rate = floatval($paramstbiro['tbi_rate3']);
                                    $tbi_commission = floatval($paramstbiro['tbi_commission3']);
                                    $tbi_insurance = floatval($paramstbiro['tbi_insurance3']);
                                    $tbi_months = intval($paramstbiro['tbi_months3']);
                                }else{
                                    if ($tbiro_price >= $tbi_divider3 && $tbiro_price < $tbi_divider4){
                                        $tbi_rate = floatval($paramstbiro['tbi_rate4']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission4']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance4']);
                                        $tbi_months = intval($paramstbiro['tbi_months4']);
                                    }else{
                                        $tbi_rate = floatval($paramstbiro['tbi_rate5']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission5']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance5']);
                                        $tbi_months = intval($paramstbiro['tbi_months5']);
                                    }
                                }
                            }
                        }
                    }else{
                        if ($tbi_divider3_is){
                            //divider 3
                            if ($tbiro_price < $tbi_divider){
                                $tbi_rate = floatval($paramstbiro['tbi_rate']);
                                $tbi_commission = floatval($paramstbiro['tbi_commission']);
                                $tbi_insurance = floatval($paramstbiro['tbi_insurance']);
                                $tbi_months = intval($paramstbiro['tbi_months']);
                            }else{
                                if ($tbiro_price >= $tbi_divider && $tbiro_price < $tbi_divider2){
                                    $tbi_rate = floatval($paramstbiro['tbi_rate2']);
                                    $tbi_commission = floatval($paramstbiro['tbi_commission2']);
                                    $tbi_insurance = floatval($paramstbiro['tbi_insurance2']);
                                    $tbi_months = intval($paramstbiro['tbi_months2']);
                                }else{
                                    if ($tbiro_price >= $tbi_divider2 && $tbiro_price < $tbi_divider3){
                                        $tbi_rate = floatval($paramstbiro['tbi_rate3']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission3']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance3']);
                                        $tbi_months = intval($paramstbiro['tbi_months3']);
                                    }else{
                                        $tbi_rate = floatval($paramstbiro['tbi_rate4']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission4']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance4']);
                                        $tbi_months = intval($paramstbiro['tbi_months4']);
                                    }
                                }
                            }
                        }else{
                            if ($tbi_divider2_is){
                                //divider 2
                                if ($tbiro_price < $tbi_divider){
                                    $tbi_rate = floatval($paramstbiro['tbi_rate']);
                                    $tbi_commission = floatval($paramstbiro['tbi_commission']);
                                    $tbi_insurance = floatval($paramstbiro['tbi_insurance']);
                                    $tbi_months = intval($paramstbiro['tbi_months']);
                                }else{
                                    if ($tbiro_price >= $tbi_divider && $tbiro_price < $tbi_divider2){
                                        $tbi_rate = floatval($paramstbiro['tbi_rate2']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission2']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance2']);
                                        $tbi_months = intval($paramstbiro['tbi_months2']);
                                    }else{
                                        $tbi_rate = floatval($paramstbiro['tbi_rate3']);
                                        $tbi_commission = floatval($paramstbiro['tbi_commission3']);
                                        $tbi_insurance = floatval($paramstbiro['tbi_insurance3']);
                                        $tbi_months = intval($paramstbiro['tbi_months3']);
                                    }
                                }
                            }else{
                                //no dividers
                                if ($tbiro_price < $tbi_divider){
                                    $tbi_rate = floatval($paramstbiro['tbi_rate']);
                                    $tbi_commission = floatval($paramstbiro['tbi_commission']);
                                    $tbi_insurance = floatval($paramstbiro['tbi_insurance']);
                                    $tbi_months = intval($paramstbiro['tbi_months']);
                                }else{
                                    $tbi_rate = floatval($paramstbiro['tbi_rate2']);
                                    $tbi_commission = floatval($paramstbiro['tbi_commission2']);
                                    $tbi_insurance = floatval($paramstbiro['tbi_insurance2']);
                                    $tbi_months = intval($paramstbiro['tbi_months2']);
                                }
                            }
                        }
                    }
                }

                if ($tbi_rate == 0){
                    $tbi_rate = 1;
                }
                $tbiro_mesecna = $this->tbiro_PMT(($tbi_rate / 100) / 12, $tbi_months, - ($tbiro_price + $tbi_commission) * (1 + $tbi_insurance * $tbi_months));
                if ($paramstbiro['tbi_backurl'] != ''){
                    if (preg_match("#https?://#", $paramstbiro['tbi_backurl']) === 0) {
                        $tbi_backurl = 'http://'.$paramstbiro['tbi_backurl'];
                    }else{
                        $tbi_backurl = $paramstbiro['tbi_backurl'];
                    }
                }else{
                    $tbi_backurl = '';
                }
                $this->context->smarty->assign(
                    array(
                        'unicid' => $tbiro_unicid,
                        'tbiro_btn_theme' => $paramstbiro['tbi_btn_theme'],
                        'tbiro_custom_button_status' => $paramstbiro['tbi_custom_button_status'],
                        'tbiro_zaglavie' => $paramstbiro['tbi_zaglavie'],
                        'tbiro_opisanie' => $paramstbiro['tbi_opisanie'],
                        'tbiro_product' => $paramstbiro['tbi_product'],
                        'tbiro_mod_version' => $this->version,
                        'tbiro_backurl' => $tbi_backurl,
                        'tbiro_vnoska' => $paramstbiro['tbi_vnoska'],
                        'tbi_btn_color' => $tbi_btn_color,
                        'tbiro_mesecna' => number_format($tbiro_mesecna, 2, '.', ''),
                        'tbi_months' => $tbi_months,
                        'tbiro_liveurl' => TBIRO_LIVEURL,
                        'tbiro_btnvisible' => $paramstbiro['tbi_btnvisible'],
                        'tbiro_status_local' => $tbiro_status
                    )
                );
            }

            if ($this->context->controller->php_self == 'product'){
                if ($paramstbiro['tbi_status'] == 'Yes' && ($tbiro_price >= $paramstbiro['tbi_minstojnost']) && ($tbiro_price <= $paramstbiro['tbi_maxstojnost'])){
                    return $this->display(__FILE__, 'tbiropayment.tpl');
                }else{
                    return null;
                }
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    public function hookDisplayHome($params) {
        $tbiro_unicid = (string)Configuration::get('CREDITTBIRO_UNICID');
        $tbiro_ch = curl_init();
        curl_setopt($tbiro_ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($tbiro_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tbiro_ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($tbiro_ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($tbiro_ch, CURLOPT_URL, TBIRO_LIVEURL . '/function/getparameters.php?cid='.$tbiro_unicid);
        $paramstbiro=json_decode(curl_exec($tbiro_ch), true);
        curl_close($tbiro_ch);

        $tbiro_status = (string)Configuration::get('CREDITTBIRO_STATUS');
        $this->context->smarty->assign(
            array(
                'tbiro_logo' => $this->_path.'css/tbiro_logo.png',
                'tbiro_status_local' => $tbiro_status,
                'tbiro_picture' => TBIRO_LIVEURL . '/calculators/assets/img/tbim' . $paramstbiro['tbi_container_reklama'] . '.png',
                'tbiro_container_status' => $paramstbiro['tbi_container_status'],
                'tbiro_status' => $paramstbiro['tbi_status'],
                'tbiro_container_txt1' => $paramstbiro['tbi_container_txt1'],
                'tbiro_container_txt2' => $paramstbiro['tbi_container_txt2'],
                'tbiro_liveurl' => TBIRO_LIVEURL
            )
        );

        return $this->display(__FILE__, 'tbipanel.tpl');
    }

    public function hookPaymentOptions($params) {
        if (empty($params['cart'])) {
            return [];
        }

        /** @var Cart $cart */
        $cart = $params['cart'];

        if ($cart->isVirtualCart()) {
            return [];
        }

        $tbiro_unicid = (string)Configuration::get('CREDITTBIRO_UNICID');
        $tbiro_status = (string)Configuration::get('CREDITTBIRO_STATUS');

        if ($tbiro_status == 0)
            return;

        $tbiro_ch = curl_init();
        curl_setopt($tbiro_ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($tbiro_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tbiro_ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($tbiro_ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($tbiro_ch, CURLOPT_URL, TBIRO_LIVEURL . '/function/getparameters.php?cid='.$tbiro_unicid);
        $paramstbiro=json_decode(curl_exec($tbiro_ch), true);
        curl_close($tbiro_ch);
        $tbi_status = $paramstbiro['tbi_status'];
        $iris_status = $paramstbiro['iris_status'];

        $cart = $this->context->cart;
        $tbi_price = floatval($cart->getordertotal(true));
        $tbi_minstojnost = floatval($paramstbiro['tbi_minstojnost']);
        $tbi_maxstojnost = floatval($paramstbiro['tbi_maxstojnost']);
        if ($tbi_status == 'Yes' && $tbi_price >= $tbi_minstojnost && $tbi_price <= $tbi_maxstojnost){
            $tbi_status_show = 'Yes';
        }else{
            $tbi_status_show = 'No';
        }

        $payment_options = [];

        if ($tbi_status_show == 'Yes'){
            $newOption_TBI = new PaymentOption();
            $newOption_TBI->setModuleName($this->name);
            $newOption_TBI->setCallToActionText((string)Configuration::get('CREDITTBIRO_FIRSTLABEL'));
            $newOption_TBI->setAction($this->context->link->getModuleLink($this->name, 'validation', ['tbiro_type' => 'TBI'], true));
            $newOption_TBI->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/css/logo.png'));
            $newOption_TBI->setAdditionalInformation((string)Configuration::get('CREDITTBIRO_SECONDLABEL') . '<br /><br />');
            $payment_options[] = $newOption_TBI;
        }

        if ($iris_status == 'Yes'){
            $newOption_IRIS = new PaymentOption();
            $newOption_IRIS->setModuleName($this->name);
            $newOption_IRIS->setCallToActionText((string)Configuration::get('CREDITIRISRO_FIRSTLABEL'));
            $newOption_IRIS->setAction($this->context->link->getModuleLink($this->name, 'validation', ['tbiro_type' => 'IRIS'], true));
            $newOption_IRIS->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/css/logo.png'));
            $newOption_IRIS->setAdditionalInformation((string)Configuration::get('CREDITIRISRO_SECONDLABEL') . '<br /><br />');
            $payment_options[] = $newOption_IRIS;
        }

        return $payment_options;
    }

    public function hookPaymentReturn($params) {
        $tbiro_url = '';
        $tbiro_output64 = '';

        if (!$this->active)
            return;

        $state = $params['order']->getCurrentState();
        if (in_array($state, array(Configuration::get('PS_OS_TBIROPAYMENT'), Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')))){
            $tbiro_encrypt = Tools::getValue('enc');
            $tbiro_envurl = Tools::getValue('envurl');
            $tbi_pause_txt = Tools::getValue('tbi_pause_txt');
            $tbiro_type = Tools::getValue('tbiro_type');
            $tbiro_env = Tools::getValue('tbiro_env');

            $tbiro_plaintext = base64_decode($tbiro_encrypt);
            $cookie = new Cookie('tbiropayment_cookie');
            $send_tbiro = $cookie->send_tbiro;

            if (($tbiro_encrypt != '') && ($send_tbiro == 'No')){
                $cookie->send_tbiro = 'Yes';
                if ($tbiro_envurl != ''){
                    $tbiro_username = (string)Configuration::get('CREDITTBIRO_USERNAME');
                    $tbiro_password = (string)Configuration::get('CREDITTBIRO_PASSWORD');
                    $tbiro_iris_key = (string)Configuration::get('CREDITTBIRO_IRIS_KEY');

                    if ($tbiro_type == 'TBI'){
                        $tbiro_publicKey = openssl_pkey_get_public(file_get_contents(_PS_MODULE_DIR_ . 'tbiropayment/keys/public.key'));
                        $tbiro_a_key = openssl_pkey_get_details($tbiro_publicKey);
                        $tbiro_chunkSize = (int)ceil($tbiro_a_key['bits'] / 8) - 11;
                        $tbiro_output = '';
                        while ($tbiro_plaintext) {
                            $tbiro_chunk = substr($tbiro_plaintext, 0, $tbiro_chunkSize);
                            $tbiro_plaintext = substr($tbiro_plaintext, $tbiro_chunkSize);
                            $tbiro_encrypted = '';
                            if (!openssl_public_encrypt($tbiro_chunk, $tbiro_encrypted, $tbiro_publicKey)) {
                                die('Failed to encrypt data');
                            }
                            $tbiro_output .= $tbiro_encrypted;
                        }
                        if (intval(substr(phpversion(), 0, 1)) < 8){
                            openssl_free_key($tbiro_publicKey);
                        }
                        $tbiro_output64 = base64_encode($tbiro_output);
                    }

                    if ($tbiro_type == 'IRIS'){
                        $curl_application = curl_init();
                            curl_setopt_array($curl_application, array(
                            CURLOPT_URL => $tbiro_envurl . '/' . $tbiro_iris_key,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 4,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => $tbiro_plaintext,
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json'
                            ),
                        ));
                        $response_application = curl_exec($curl_application);
                        curl_close($curl_application);
                        $tbiroiris_result = json_decode($response_application);
                        if ($tbiroiris_result->paymentLink != null){
                            $tbiro_url = $tbiroiris_result->paymentLink;
                            //var_dump($tbigriris_plaintext);
                            //var_dump($tbigriris_result);
                            //die;
                        }
                    }
                }
            }else{
                die($this->trans('Communication problem or You have already sent this order!', [], 'Modules.Tbiropayment.Admin'));
            }

            $tbiro_total_to_pay = Tools::displayPrice($params['order']->getOrdersTotalPaid(), new Currency($params['order']->id_currency), false);
            $this->smarty->assign(array(
                'tbiro_total_to_pay' => $tbiro_total_to_pay,
                'tbiro_status' => 'ok',
                'tbiro_output64' => $tbiro_output64,
                'tbiro_envurl' => $tbiro_envurl,
                'tbi_pause_txt' => $tbi_pause_txt,
                'tbirologo' => __PS_BASE_URI__ . 'modules/tbiropayment/css/logo.png',
                'type' => strtoupper($tbiro_type),
                'tbiro_url' => $tbiro_url
            ));
        }else{
            $this->smarty->assign('status', 'failed');
        }
        return $this->display(__FILE__, 'payment_return.tpl');
    }

}
