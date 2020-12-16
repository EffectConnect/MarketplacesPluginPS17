<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use EffectConnect\Marketplaces\LegacyWrappers\LegacyModule;
use PrestaShop\PrestaShop\Adapter\Module\PaymentModuleListProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopDatabaseException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PaymentModuleChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
final class PaymentModuleChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $_translator;

    /**
     * @var PaymentModuleListProvider
     */
    protected $_paymentModuleListProvider;

    /**
     * PaymentModuleChoiceProvider constructor.
     * @param TranslatorInterface $translator
     * @param PaymentModuleListProvider $paymentModuleListProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        PaymentModuleListProvider $paymentModuleListProvider
    )
    {
        $this->_translator                = $translator;
        $this->_paymentModuleListProvider = $paymentModuleListProvider;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = ['' => ''];

        foreach ($this->_paymentModuleListProvider->getPaymentModuleList() as $paymentModule)
        {
            $moduleId                                                = intval($paymentModule->database->get('id'));
            $choices[$paymentModule->attributes->get('displayName')] = $moduleId; // TODO: are these module names translatable?
        }

        return $choices;
    }

    /**
     * @return array
     */
    public function getChoicesAttributes()
    {
        $attributes = [];

        foreach ($this->_paymentModuleListProvider->getPaymentModuleList() as $paymentModule)
        {
            $moduleId  = intval($paymentModule->database->get('id'));
            $model     = new LegacyModule();
            $model->id = $moduleId;
            try {
                $shops = $model->getAssociatedShops();
            } catch (PrestaShopDatabaseException $e) {
                $shops = [];
            }

            $attributes[$paymentModule->attributes->get('displayName')] = ['data-shop-id' => json_encode($shops)];; // TODO: are these module names translatable?
        }

        return $attributes;
    }
}
