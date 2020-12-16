<?php

namespace EffectConnect\Marketplaces\Form;

use EffectConnect\Marketplaces\Model\Connection;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Validate;

/**
 * Class AdminConnectionDataProvider
 * @package EffectConnect\Marketplaces\Form
 */
class AdminConnectionDataProvider implements FormDataProviderInterface
{
   /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        $objectPresenter = new ObjectPresenter();

        $record = new Connection(intval($id));
        if (Validate::isLoadedObject($record)) {
            return $objectPresenter->present($record);
        }

        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $objectPresenter = new ObjectPresenter();

        $record = new Connection();
        return $objectPresenter->present($record);
    }
}
