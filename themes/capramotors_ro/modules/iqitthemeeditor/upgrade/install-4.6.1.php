<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_6_1($object)
{

    Configuration::updateValue($object->cfgName . 'mm_hf_header_visibilty', 1);
    Configuration::updateValue($object->cfgName . 'mm_hf_header_typo', '{"size":"22","bold":null,"italic":null,"uppercase":null,"spacing":"0"}');

    return true;

}

