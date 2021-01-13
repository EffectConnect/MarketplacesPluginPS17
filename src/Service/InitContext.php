<?php

namespace EffectConnect\Marketplaces\Service;

use AdminController;
use Context;
use Currency;
use EffectConnect\Marketplaces\Exception\InitContextFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopException;
use PrestaShopDatabaseException;
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
     * @param Connection $connection
     * @throws InitContextFailedException
     */
    public function initContext(Connection $connection)
    {
        try {
            // We need to have an employee or the module hooks don't work (see LegacyHookSubscriber).
            if (!$this->_legacyContext->getContext()->employee) {
                $employee = new Employee($connection->order_import_id_employee);

                if (intval($employee->id) === 0) {
                    throw new InitContextFailedException('Employee ' . intval($connection->order_import_id_employee). ' init failed (employee does not exist)');
                }

                $this->_legacyContext->getContext()->employee = $employee;
            }
        } catch (PrestaShopException $e) {
            throw new InitContextFailedException('Employee ' . intval($connection->order_import_id_employee). ' init failed (PrestaShopException)');
        } catch (PrestaShopDatabaseException $e) {
            throw new InitContextFailedException('Employee ' . intval($connection->order_import_id_employee). ' init failed (PrestaShopDatabaseException)');
        }

        try {
            // Also we need to have a controller type.
            $adminController = new AdminController();
            $adminController->initShopContext();
        } catch (PrestaShopException $e) {
            throw new InitContextFailedException('Shop init failed');
        }
    }

    /**
     * @param int $idShop
     * @throws InitContextFailedException
     */
    public function setShop(int $idShop)
    {
        try {
            $this->_legacyContext->getContext()->shop = new Shop($idShop);
            Shop::setContext(Shop::CONTEXT_SHOP, $idShop);
        } catch (PrestaShopException $e) {
            throw new InitContextFailedException('Shop set ' . intval($idShop) . ' failed');
        }
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