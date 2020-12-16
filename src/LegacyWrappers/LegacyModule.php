<?php

namespace EffectConnect\Marketplaces\LegacyWrappers;

use ObjectModel;

/**
 * This class' purposes is to be able to execute the core PS function ObjectModel::getAssociatedShops for a module.
 * The PS Module class does not extend ObjectModel, which makes it impossible to get associated shops for a module out of the box.
 *
 * @package EffectConnect\Marketplaces\LegacyWrappers
 */
class LegacyModule extends ObjectModel
{
    /**
     * @var string[]
     */
    public static $definition = [
        'table'   => 'module',
        'primary' => 'id_module',
    ];
}