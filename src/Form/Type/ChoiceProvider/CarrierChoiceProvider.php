<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use Carrier;
use EffectConnect\Marketplaces\Service\InitContext;
use Exception;
use PrestaShopDatabaseException;

/**
 * Class CarrierChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
class CarrierChoiceProvider
{
    /**
     * @var InitContext
     */
    protected $_initContext;

    /**
     * GroupChoiceProvider constructor.
     * @param InitContext $initContext
     */
    public function __construct(InitContext $initContext)
    {
        $this->_initContext = $initContext;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        $carriersChoices = ['' => ''];

        try {
            $carriers = Carrier::getCarriers($this->_initContext->getContext()->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
        } catch (Exception $e) {
            $carriers = [];
        }

        foreach ($carriers as $carrierArray)
        {
            $carriersChoices[$carrierArray['name']] = $carrierArray['id_carrier'];
        }

        return $carriersChoices;
    }

    /**
     * @return array
     */
    public function getChoicesAttributes()
    {
        $carriersAttributes = [];

        try {
            $carriers = Carrier::getCarriers($this->_initContext->getContext()->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
        } catch (Exception $e) {
            $carriers = [];
        }

        foreach ($carriers as $carrierArray)
        {
            $model = new Carrier();
            $model->id = $carrierArray['id_carrier'];
            try {
                $shops = $model->getAssociatedShops();
            } catch (PrestaShopDatabaseException $e) {
                $shops = [];
            }

            $carriersAttributes[$carrierArray['name']] = ['data-shop-id' => json_encode($shops)];
        }

        return $carriersAttributes;
    }
}
