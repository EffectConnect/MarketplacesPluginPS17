<?php

namespace EffectConnect\Marketplaces\Service;

use AdminController;
use Context;
use Currency;
use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopException;
use Shop;

/**
 * Class InitContext
 * @package EffectConnect\Marketplaces\Service
 */
class InitContext
{
    /**
     * @var LegacyContext
     */
    protected $_legacyContext;

    /**
     * InitContext constructor.
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        LegacyContext $legacyContext
    ) {
        $this->_legacyContext = $legacyContext;
    }

    /**
     * @throws PrestaShopException
     */
    public function initContext()
    {
        // We need to have an employee or the module hooks don't work (see LegacyHookSubscriber).
        if (!$this->_legacyContext->getContext()->employee) {
            // Even a non existing employee is fine.
            $this->_legacyContext->getContext()->employee = new Employee();
        }

        // Also we need to have a controller type.
        $adminController = new AdminController();
        $adminController->initShopContext();
    }

    /**
     * @param int $idShop
     * @throws PrestaShopException
     */
    public function setShop(int $idShop)
    {
        $this->_legacyContext->getContext()->shop = new Shop($idShop);
        Shop::setContext(Shop::CONTEXT_SHOP, $idShop);
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->_legacyContext->getContext()->currency = $currency;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->_legacyContext->getContext();
    }
}