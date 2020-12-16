<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use EffectConnect\Marketplaces\Enums\ExternalFulfilment;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ExternalFulfilmentChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
final class ExternalFulfilmentChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $_translator;

    /**
     * EanChoiceProvider constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        // Choices for 'order_import_external_fulfilment'.
        return [
            $this->_translator->trans('Only import internal orders', [], 'Modules.Effectconnectmarketplaces.Admin')
                => ExternalFulfilment::INTERNAL_ORDERS,
            $this->_translator->trans('Only import orders that are fulfilled externally', [], 'Modules.Effectconnectmarketplaces.Admin')
                => ExternalFulfilment::EXTERNAL_ORDERS,
            $this->_translator->trans('Import both internal orders and orders that are fulfilled externally', [], 'Modules.Effectconnectmarketplaces.Admin')
                => ExternalFulfilment::EXTERNAL_AND_INTERNAL_ORDERS
        ];
    }
}
