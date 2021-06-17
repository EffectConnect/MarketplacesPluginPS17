<?php

use EffectConnect\Marketplaces\Model\TrackingExportQueue;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_1_14($object)
{
    return TrackingExportQueue::addDbFieldOrderImportedAt();
}
