<?php

use EffectConnect\Marketplaces\Model\Connection;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_1_17($object)
{
    return Connection::addDbFieldCatalogExportUnavailableForOrder();
}
