<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ShopTreeChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ShopChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
final class ShopChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ShopTreeChoiceProvider
     */
    protected $_shopTreeChoiceProvider;

    /**
     * ShopChoiceProvider constructor.
     * @param ShopTreeChoiceProvider $shopTreeChoiceProvider
     */
    public function __construct(
        ShopTreeChoiceProvider $shopTreeChoiceProvider
    ) {
        $this->_shopTreeChoiceProvider = $shopTreeChoiceProvider;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        $shopGroups = $this->_shopTreeChoiceProvider->getChoices();
        $shopChoices = [];
        foreach ($shopGroups as $shopGroup) {
            $shops = [];
            foreach ($shopGroup['children'] as $shop) {
                $shopName         = $shop['name'];
                $shopId           = $shop['id_shop'];
                $shops[$shopName] = $shopId;
            }
            $shopGroupName = $shopGroup['name'];
            $shopChoices[$shopGroupName] = $shops;
        }
        return $shopChoices;
    }
}
