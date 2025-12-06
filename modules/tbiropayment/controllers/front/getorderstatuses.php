<?php
class TbiropaymentGetorderstatusesModuleFrontController extends ModuleFrontController
{
    public $result = array();
    
    public function initContent()
    {
        $this->ajax = true;
        $this->result['status'] = '';
        $tbiro_orderdata_all = '';
        
        if (file_exists(_PS_MODULE_DIR_ . 'tbiropayment/keys/tbiroorders.json')) {
            $orderdata = file_get_contents(_PS_MODULE_DIR_ . 'tbiropayment/keys/tbiroorders.json');
            $tbiro_orderdata_all = json_decode($orderdata, true);
        }
        $this->result['status'] = $tbiro_orderdata_all;
        
        parent::initContent();
    }
    
    public function initHeader(){
        header('Access-Control-Allow-Origin: *');
        return parent::initHeader();
    }
    
    public function displayAjax()
    {
        die(json_encode($this->result, JSON_UNESCAPED_UNICODE));
    }
}
