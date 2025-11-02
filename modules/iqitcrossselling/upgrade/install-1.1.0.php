<?php
/**
 * 2007-2015 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2007-2015 IQIT-COMMERCE.COM
 *  @license   GNU General Public License version 2
 *
 * You can not resell or redistribute this software.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($object)
{
    Configuration::updateValue('IQITCROSSSELLING_NBR', 10);
    Configuration::updateValue('IQITCROSSSELLING_HIDE_OOSP', 0);
    return true;
}
