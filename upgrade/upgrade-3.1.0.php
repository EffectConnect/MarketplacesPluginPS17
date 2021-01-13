<?php

use EffectConnect\Marketplaces\Model\Connection;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_1_0($object)
{
    return Connection::addDbFieldOrderImportIdEmployee();
}
