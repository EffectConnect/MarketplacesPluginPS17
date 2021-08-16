<?php

namespace EffectConnect\Marketplaces\Form;

use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\CarrierChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\EmployeeChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\ExternalFulfilmentChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\GroupChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\InvalidEANChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\PaymentModuleChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\ShopChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Carrier\CarrierDataProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminConnectionFormType
 * @package EffectConnect\Marketplaces\Form
 */
class AdminConnectionFormType extends TranslatorAwareType
{
    /**
     * @var ShopChoiceProvider
     */
    protected $_shopChoiceProvider;

    /**
     * @var InvalidEANChoiceProvider
     */
    protected $_invalidEANChoiceProvider;

    /**
     * @var CarrierDataProvider
     */
    protected $_carrierChoiceProvider;

    /**
     * @var PaymentModuleChoiceProvider
     */
    protected $_paymentModuleChoiceProvider;

    /**
     * @var ExternalFulfilmentChoiceProvider
     */
    protected $_externalFulfilmentChoiceProvider;

    /**
     * @var GroupChoiceProvider
     */
    protected $_groupChoiceProvider;

    /**
     * @var EmployeeChoiceProvider
     */
    protected $_employeeChoiceProvider;

    /**
     * AdminConnectionFormType constructor.
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ShopChoiceProvider $shopChoiceProvider
     * @param InvalidEANChoiceProvider $invalidEANChoiceProvider
     * @param CarrierChoiceProvider $carrierChoiceProvider
     * @param PaymentModuleChoiceProvider $paymentModuleChoiceProvider
     * @param ExternalFulfilmentChoiceProvider $externalFulfilmentChoiceProvider
     * @param GroupChoiceProvider $groupChoiceProvider
     * @param EmployeeChoiceProvider $employeeChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ShopChoiceProvider $shopChoiceProvider,
        InvalidEANChoiceProvider $invalidEANChoiceProvider,
        CarrierChoiceProvider $carrierChoiceProvider,
        PaymentModuleChoiceProvider $paymentModuleChoiceProvider,
        ExternalFulfilmentChoiceProvider $externalFulfilmentChoiceProvider,
        GroupChoiceProvider $groupChoiceProvider,
        EmployeeChoiceProvider $employeeChoiceProvider
    ) {
        $this->_shopChoiceProvider               = $shopChoiceProvider;
        $this->_invalidEANChoiceProvider         = $invalidEANChoiceProvider;
        $this->_carrierChoiceProvider            = $carrierChoiceProvider;
        $this->_paymentModuleChoiceProvider      = $paymentModuleChoiceProvider;
        $this->_externalFulfilmentChoiceProvider = $externalFulfilmentChoiceProvider;
        $this->_groupChoiceProvider              = $groupChoiceProvider;
        $this->_employeeChoiceProvider           = $employeeChoiceProvider;
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO: custom action for testing credentials?
        $builder
            // General fields
            ->add('id_connection', HiddenType::class)
            ->add('is_active', SwitchType::class, [
                'label'      => $this->trans('Active', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('No export and import processes will run for inactive connections.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('id_shop', ChoiceType::class, [
                'choices'    => $this->_shopChoiceProvider->getChoices(),
                'required'   => true,
                'label'      => $this->trans('Shop', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Select the shop you want the catalog to export for. All languages within the shop are automatically exported.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('name', TextType::class, [
                'required'   => true,
                'label'      => $this->trans('Name', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Used for internal reference only.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('public_key', TextType::class, [
                'required'   => true,
                'label'      => $this->trans('Public key', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Find your public key in the API Key Management section in EffectConnect Marketplaces.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('secret_key', TextType::class, [
                'required'   => true,
                'label'      => $this->trans('Secret key', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Find your secret key in the API Key Management section in EffectConnect Marketplaces.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            // Catalog export fields
            ->add('catalog_export_only_active', SwitchType::class, [
                'required'   => true,
                'label'      => $this->trans('Only export active products', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('If this setting is enabled only products that are enabled will be exported to EffectConnect.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('catalog_export_skip_unavailable_for_order', SwitchType::class, [
                'required'   => true,
                'label'      => $this->trans('Only export products that are unavailable for order', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('If this setting is enabled only products that are available for order will be exported to EffectConnect.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('catalog_export_special_price', SwitchType::class, [
                'required'   => true,
                'label'      => $this->trans('Use special price', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('If enabled the special price and the original price will be exported to EffectConnect Marketplaces. If disabled only the original price will be exported.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('catalog_export_add_option_title', SwitchType::class, [
                'required'   => true,
                'label'      => $this->trans('Add product combinations to product title', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Will add a summary of product combinations to the product title. For example: "Hummingbird printed t-shirt (Size - S, Color - White)". ', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('catalog_export_ean_leading_zero', SwitchType::class, [
                'required'   => true,
                'label'      => $this->trans('Add leading zero to EAN', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Select whether the plugin should automatically add a leading zero to an EAN that consists of 12 characters. Products with invalid EAN will be exported without EAN field.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('catalog_export_skip_invalid_ean', ChoiceType::class, [
                'choices'    => $this->_invalidEANChoiceProvider->getChoices(),
                'required'   => true,
                'label'      => $this->trans('When EAN invalid', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('EffectConnect only supports products with a valid EAN or products without an EAN. Multiple products with the same EAN will only be exported once.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            // Order import fields
            ->add('order_import_id_group', ChoiceType::class, [
                'choices'     => $this->_groupChoiceProvider->getChoices(),
                'choice_attr' => $this->_groupChoiceProvider->getChoicesAttributes(),
                'required'    => true,
                'constraints' => [new NotBlank()],
                'label'       => $this->trans('Customer group', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'        => $this->trans('For example you can use a specific customer group to be able to select a carrier that is only used for importing orders.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('order_import_id_carrier', ChoiceType::class, [
                'choices'     => $this->_carrierChoiceProvider->getChoices(),
                'choice_attr' => $this->_carrierChoiceProvider->getChoicesAttributes(),
                'required'    => true,
                'constraints' => [new NotBlank()],
                'label'       => $this->trans('Shipping method', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'        => $this->trans('The shipment method for each imported order.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('order_import_id_payment_module', ChoiceType::class, [
                'choices'     => $this->_paymentModuleChoiceProvider->getChoices(),
                'choice_attr' => $this->_paymentModuleChoiceProvider->getChoicesAttributes(),
                'required'    => true,
                'constraints' => [new NotBlank()],
                'label'       => $this->trans('Payment method', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'        => $this->trans('The payment method for each imported order.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('order_import_id_employee', ChoiceType::class, [
                'choices'     => $this->_employeeChoiceProvider->getChoices(),
                'choice_attr' => $this->_employeeChoiceProvider->getChoicesAttributes(),
                'required'    => true,
                'constraints' => [new NotBlank()],
                'label'       => $this->trans('Employee', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'        => $this->trans('The employee each imported order is assigned to (used by Prestashop for order history and stock update logs)', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('order_import_external_fulfilment', ChoiceType::class, [
                'choices'    => $this->_externalFulfilmentChoiceProvider->getChoices(),
                'required'   => true,
                'label'      => $this->trans('External fulfilment', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Whether to import orders that are externally fulfilled or not. Internally fulfilled orders have status paid in EffectConnect. Externally fulfilled orders have status completed and tag external_fulfilled.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('order_import_send_emails', SwitchType::class, [
                'required'   => true,
                'label'      => $this->trans('Send emails', 'Modules.Effectconnectmarketplaces.Admin'),
                'help'       => $this->trans('Whether or not to let Prestashop send order update emails to the customer for each imported order.', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Modules.Effectconnectmarketplaces.Admin',
        ]);
    }
}
