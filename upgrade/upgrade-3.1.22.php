<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_1_22($object)
{
    return true; // Tell main module file to run runUpgradeModule() function (translations were added)
}
