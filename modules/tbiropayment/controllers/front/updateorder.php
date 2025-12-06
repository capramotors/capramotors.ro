<?php
class TbiropaymentUpdateorderModuleFrontController extends ModuleFrontController
{
    public $result = array();
    
    public function initContent()
    {
        $this->ajax = true;
        $this->result['success'] = 'unsuccess';
        
        $tbiro_unicid = (string)Configuration::get('CREDITTBIRO_UNICID');
        
        if (null !== Tools::getValue('order_id')) {
            $tbiro_order_id = Tools::getValue('order_id');
        } else {
            $tbiro_order_id = '';
        }
        if (null !== Tools::getValue('status')) {
            $tbiro_status = Tools::getValue('status');
        } else {
            $tbiro_status = '';
        }
        if (null !== Tools::getValue('calculator_id')) {
            $tbiro_calculator_id = Tools::getValue('calculator_id');
        } else {
            $tbiro_calculator_id = '';
        }
        
        if (($tbiro_calculator_id != '') && ($tbiro_unicid == $tbiro_calculator_id)){
            if (file_exists(_PS_MODULE_DIR_ . 'tbiropayment/keys/tbiroorders.json')) {
                $orderdata = file_get_contents(_PS_MODULE_DIR_ . 'tbiropayment/keys/tbiroorders.json');
                $tbiro_orderdata_all = json_decode($orderdata, true);
                foreach ($tbiro_orderdata_all as $key => $value){
                    if ($tbiro_orderdata_all[$key]['order_id'] == $tbiro_order_id){
                        $tbiro_orderdata_all[$key]['order_status'] = $tbiro_status;
                    }
                }
                $jsondata = json_encode($tbiro_orderdata_all);
                file_put_contents(_PS_MODULE_DIR_ . 'tbiropayment/keys/tbiroorders.json', $jsondata);
                $this->result['success'] = 'success';
            }
        }
        
        $this->result['tbiro_order_id'] = $tbiro_order_id;
        $this->result['tbiro_status'] = $tbiro_status;
        $this->result['tbiro_calculator_id'] = $tbiro_calculator_id;
        
        parent::initContent();
    }
    
    public function initHeader(){
        header('Access-Control-Allow-Origin: https://tbicp.com');
        return parent::initHeader();
    }
    
    public function displayAjax()
    {
        die(json_encode($this->result, JSON_UNESCAPED_UNICODE));
    }
}
