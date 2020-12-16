<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use EffectConnect\Marketplaces\Service\InitContext;
use Exception;
use Group;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopDatabaseException;

/**
 * Class GroupChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
class GroupChoiceProvider implements FormChoiceProviderInterface
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
        $groupChoices = ['' => ''];

        try {
            $groups = Group::getGroups($this->_initContext->getContext()->language->id);
        } catch (Exception $e) {
            $groups = [];
        }

        foreach ($groups as $groupArray)
        {
            $groupChoices[$groupArray['name']] = $groupArray['id_group'];
        }

        return $groupChoices;
    }

    /**
     * @return array
     */
    public function getChoicesAttributes()
    {
        $groupAttributes = [];

        try {
            $groups = Group::getGroups($this->_initContext->getContext()->language->id);
        } catch (Exception $e) {
            $groups = [];
        }

        foreach ($groups as $groupArray)
        {
            $model     = new Group();
            $model->id = $groupArray['id_group'];
            try {
                $shops = $model->getAssociatedShops();
            } catch (PrestaShopDatabaseException $e) {
                $shops = [];
            }

            $groupAttributes[$groupArray['name']] = ['data-shop-id' => json_encode($shops)];
        }

        return $groupAttributes;
    }
}
