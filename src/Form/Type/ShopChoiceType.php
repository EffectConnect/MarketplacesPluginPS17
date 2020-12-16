<?php

namespace EffectConnect\Marketplaces\Form\Type;

use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\ShopChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShopChoiceType
 * @package EffectConnect\Marketplaces\Form\Type
 */
class ShopChoiceType extends CommonAbstractType
{
    /**
     * @var ShopChoiceProvider
     */
    protected $_shopChoiceProvider;

    /**
     * ShopChoiceType constructor.
     * @param ShopChoiceProvider $shopChoiceProvider
     */
    public function __construct(
        ShopChoiceProvider $shopChoiceProvider
    ) {
        $this->_shopChoiceProvider = $shopChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'  => $this->_shopChoiceProvider->getChoices(),
            'required' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
