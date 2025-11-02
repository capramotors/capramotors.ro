<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_6_2($object)
{

    Configuration::updateValue($object->cfgName . 'sm_tiktok', '');

    return true;

}

