<?php

namespace EffectConnect\Marketplaces\LegacyWrappers;

use Shop;

/**
 * Class LegacyShopContext
 * @package EffectConnect\Marketplaces\LegacyWrappers
 */
class LegacyShopContext
{
    /**
     * In connection add/edit forms, the list of carriers and payment methods depends on the selected shop context.
     * Since this plugin does not use the shop context, we have to make sure we are in 'All shops' context when
     * multishop feature is active. When this feature is not active, there is no issue.
     *
     * @return bool
     */
    public function isAllOrOnlyShopContext()
    {
        return (!Shop::isFeatureActive() || Shop::getContext() === Shop::CONTEXT_ALL);
    }
}