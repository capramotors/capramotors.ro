<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminIqitAdditionalTabsController extends ModuleAdminController
{
    public function ajaxProcessUpdatePositions()
    {
        $tabs = Tools::getValue('tabs');
        IqitAdditionalTab::updatePositions($tabs);
        $this->module->clearCache();
        die(true);
    }

    public function ajaxProcessUpdatePositionsProduct()
    {
        $tabs = Tools::getValue('iqitadditionaltabs');
        IqitAdditionalTab::updatePositions($tabs);
        $this->module->clearCache();
        die(true);
    }

    public function ajaxProcessAddTabProduct()
    {
        header('Content-Type: application/json');

        parse_str(Tools::getValue('fields'), $fields);

        $idProduct = Tools::getValue('idProduct');
        $id_iqitadditionaltab = (int) $fields[$this->module->name]['id_iqitadditionaltab'];

        $action = 'add';

        if ($id_iqitadditionaltab) {
            $iqitAdditionalTab = new IqitAdditionalTab((int) $id_iqitadditionaltab);
            $action = 'edit';
        } else {
            $iqitAdditionalTab = new IqitAdditionalTab();
            $iqitAdditionalTab->id_product = $idProduct;
        }

        if (isset($fields[$this->module->name]['active'])) {
            $fields[$this->module->name]['active'] = 1;
        } else {
            $fields[$this->module->name]['active'] = 0;
        }

        $iqitAdditionalTab->copyFromAjax($fields[$this->module->name]);

        if (Shop::getContext() == Shop::CONTEXT_ALL) {
            $iqitAdditionalTab->id_shop_list = Shop::getShops(true, null, true);
        } else {
            $iqitAdditionalTab->id_shop_list[] = (int) Context::getContext()->shop->id;
        }

        if ($iqitAdditionalTab->validateFields(false) && $iqitAdditionalTab->validateFieldsLang(false)) {
            $iqitAdditionalTab->save();
            $this->module->clearCache($idProduct);
            $return = [
                'status' => true,
                'action' => $action,
                'message' => $this->module->getTranslator()->trans('Tab saved', [], 'Modules.IqitAdditionalTabs.Admin'),
                'tab' => [
                    'id' => $iqitAdditionalTab->id,
                    'title' => $iqitAdditionalTab->title,
                ],
            ];
        } else {
            $return = [
                'status' => false,
                'message' => $this->module->getTranslator()->trans('An problem occured during adding tab', [], 'Modules.IqitAdditionalTabs.Admin'),
            ];
        }

        die(json_encode($return));
    }

    public function ajaxProcessDeleteTabProduct()
    {
        $id_iqitadditionaltab = (int) Tools::getValue('id_iqitadditionaltab');

        $iqitAdditionalTab = new IqitAdditionalTab((int) $id_iqitadditionaltab);
        $iqitAdditionalTab->delete();
        $this->module->clearCache();
        die(true);
    }

    public function ajaxProcessGetTabProduct()
    {
        header('Content-Type: application/json');
        $id_iqitadditionaltab = (int) Tools::getValue('id_iqitadditionaltab');
        $iqitAdditionalTab = new IqitAdditionalTab((int) $id_iqitadditionaltab);

        $return = [
            'status' => true,
            'tab' => [
                'id' => $iqitAdditionalTab->id,
                'title' => $iqitAdditionalTab->title,
                'description' => $iqitAdditionalTab->description,
                'active' => (bool) $iqitAdditionalTab->active,
            ],
        ];

        die(json_encode($return));
    }
}
