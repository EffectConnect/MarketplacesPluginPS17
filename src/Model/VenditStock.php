<?php

namespace EffectConnect\Marketplaces\Model;

use Db;

/**
 * Model for the table ps_vms_stock.
 */
class VenditStock
{
    /**
     * id_product
     * @var int
     */
    private $_productId;
    

    /**
     * id_vms_warehouse
     * @var int
     */
    private $_warehouseId;

    /**
     * quantity
     * @var int
     */
    private $_quantity;

    /**
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     */
    public function __construct(int $productId, int $warehouseId, int $quantity)
    {
        $this->_productId = $productId;
        $this->_warehouseId = $warehouseId;
        $this->_quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->_productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId)
    {
        $this->_productId = $productId;
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->_storeId;
    }

    /**
     * @param int $storeId
     */
    public function setStoreId(int $storeId)
    {
        $this->_storeId = $storeId;
    }

    /**
     * @return int
     */
    public function getWarehouseId(): int
    {
        return $this->_warehouseId;
    }

    /**
     * @param int $warehouseId
     */
    public function setWarehouseId(int $warehouseId)
    {
        $this->_warehouseId = $warehouseId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->_quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->_quantity = $quantity;
    }
}