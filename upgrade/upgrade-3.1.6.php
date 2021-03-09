<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_1_6($object)
{
    return true; // Tell main module file to run runUpgradeModule() function
}
