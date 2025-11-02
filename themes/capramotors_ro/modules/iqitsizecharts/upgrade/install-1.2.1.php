<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_2_1($object)
{
    $object->registerHook('displayBackOfficeHeader');
	return true;
}
