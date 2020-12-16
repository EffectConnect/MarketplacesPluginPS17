<?php

namespace EffectConnect\Marketplaces\Grid;

use EffectConnect\Marketplaces\Form\Type\ShopChoiceType;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AdminConnectionGridDefinitionFactory
 * @package EffectConnect\Marketplaces\Grid
 */
final class AdminConnectionGridDefinitionFactory extends AbstractGridDefinitionFactory
{
   /**
     * @var string
     */
    private $resetFiltersUrl;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * AdminConnectionGridDefinitionFactory constructor.
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $resetFiltersUrl
     * @param string $redirectUrl
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        string $resetFiltersUrl,
        string $redirectUrl
    ) {
        $this->resetFiltersUrl = $resetFiltersUrl;
        $this->redirectUrl     = $redirectUrl;
        parent::__construct($hookDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'adminconnectiongrid';
    }

   /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Connections', [], 'Modules.Effectconnectmarketplaces.Admin');
    }

   /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new DataColumn('id_connection'))
                    ->setName($this->trans('ID', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field' => 'id_connection',
                    ])
            )
            ->add(
                (new ToggleColumn('is_active'))
                    ->setName($this->trans('Active', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field'            => 'is_active',
                        'primary_field'    => 'id_connection',
                        'route'            => 'effectconnect_marketplaces_adminconnection_active_toggle',
                        'route_param_name' => 'recordId',
                    ])
            )
            ->add(
                (new DataColumn('shop_name'))
                    ->setName($this->trans('Shop', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field' => 'shop_name',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('public_key'))
                    ->setName($this->trans('Public key', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field' => 'public_key',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            )
        ;
    }

   /**
     * {@inheritdoc}
     *
     * Define filters and associate them with columns.
     * Note that you can add filters that are not associated with any column.
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_connection', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_connection')
            )
            ->add(
                (new Filter('id_shop', ShopChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('shop_name')
            )
            ->add(
                (new Filter('is_active', YesAndNoChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('is_active')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('public_key', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('public_key')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'data-url'      => $this->resetFiltersUrl,
                            'data-redirect' => $this->redirectUrl,
                        ],
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * Extracted row action definition into separate method.
     */
    private function getRowActions()
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setOptions([
                        'route'             => 'effectconnect_marketplaces_adminconnection_edit',
                        'route_param_name'  => 'recordId',
                        'route_param_field' => 'id_connection',
                    ])
                    ->setIcon('edit')
            )
            ->add(
                (new LinkRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setOptions([
                        'route'             => 'effectconnect_marketplaces_adminconnection_delete',
                        'route_param_name'  => 'recordId',
                        'route_param_field' => 'id_connection',
                    ])
                    ->setIcon('delete')
            )
        ;
    }
}
