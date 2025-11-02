<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_2_2($object)
{
	$object->unregisterHook('Header');
    $object->registerHook('displayHeader');
	return true;
}
