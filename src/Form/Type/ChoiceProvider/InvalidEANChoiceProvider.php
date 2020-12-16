<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use EffectConnect\Marketplaces\Enums\InvalidEAN;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class InvalidEANChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
final class InvalidEANChoiceProvider implements FormChoiceProviderInterface
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
        // Choices for 'catalog_export_skip_invalid_ean' - translated as 'When EAN invalid'
        return [
            $this->_translator->trans('Export product (without its EAN)', [], 'Modules.Effectconnectmarketplaces.Admin') => InvalidEAN::PRODUCT_EXPORT_WITHOUT_EAN,
            $this->_translator->trans('Skip export of product', [], 'Modules.Effectconnectmarketplaces.Admin')           => InvalidEAN::PRODUCT_EXPORT_SKIP
        ];
    }
}
