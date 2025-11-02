<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminIqitMegaMenuController extends ModuleAdminController
{
    public function ajaxProcessUpdateHorizontalTabsPosition()
    {
        $tabs = Tools::getValue('tabs');
        foreach ($tabs as $position => $id_tab) {
            $res = Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'iqitmegamenu_tabs` SET `position` = ' . (int) $position . '
            WHERE `id_tab` = ' . (int) $id_tab . ' AND menu_type = 1');
        }
        $this->module->clearMenuCache();
    }

    public function ajaxProcessupdateVerticalTabsPosition()
    {
        $tabs = Tools::getValue('tabs');
        foreach ($tabs as $position => $id_tab) {
            $res = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'iqitmegamenu_tabs` SET `position` = ' . (int) $position . '
            WHERE `id_tab` = ' . (int) $id_tab . ' AND menu_type = 2');
        }
        $this->module->clearMenuCache();
    }

    public function ajaxProcessupdateMobileTabsPosition()
    {
        $tabs = Tools::getValue('tabs');
        foreach ($tabs as $position => $id_tab) {
            $res = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'iqitmegamenu_tabs` SET `position` = ' . (int) $position . '
            WHERE `id_tab` = ' . (int) $id_tab . ' AND menu_type = 4');
        }
        $this->module->clearMenuCache();
    }


    public function ajaxProcessSearchProducts()
    {
        header('Content-Type: application/json');

        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }
        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }
        $excludeVirtuals = false;
        $exclude_packs = false;
        $context = Context::getContext();
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
        FROM `' . _DB_PREFIX_ . 'product` p
        ' . Shop::addSqlAssociation('product', 'p') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)$context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image` image
        ON (image.`id_product` = p.`id_product` AND image.cover=1)
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$context->language->id . ')
        WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\') AND p.`active` = 1' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product
            LIMIT 50';

        $items = Db::getInstance()->executeS($sql);

        if ($items && ($excludeIds || strpos($_SERVER['HTTP_REFERER'], 'AdminScenes') !== false)) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|' . (int)($item['id_product']) . "\n";
            }
        } elseif ($items) {
            $results = array();
            foreach ($items as $item) {
                $product = array(
                    'id' => (int)($item['id_product']),
                    'name' => $item['name'],
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => str_replace('http://', Tools::getShopProtocol(),
                        $context->link->getImageLink($item['link_rewrite'], $item['id_image'],
                            ImageType::getFormattedName('medium'))),
                );
                array_push($results, $product);
            }
            $results = array_values($results);
            die(json_encode($results));
        } else {
            die(json_encode(new stdClass));
        }
    }

    
}
