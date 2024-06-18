<?php

namespace EffectConnect\Marketplaces\Helper;
use EffectConnect\Marketplaces\Model\VenditStock;
use EffectConnect\Marketplaces\Model\VenditWarehouse;
use Db;
use Throwable;

/**
 * A helper class for obtaining information about warehouses implemented by Vendit.
 */
class VenditHelper
{
    /**
     * Remembers if the platform has Vendit warehouses support.
     *
     * @var null
     */
    protected static $_hasWarehouseSupport = null;

    /**
     * Check if the platform has Vendit warehouses support.
     *
     * @return bool
     */
    public static function hasWarehouseSupport(): bool
    {
        if (self::$_hasWarehouseSupport !== null) {
            return self::$_hasWarehouseSupport;
        }

        $db = Db::getInstance();
        try {
            $numberOfWarehouses = $db->getValue("SELECT COUNT(*) FROM ps_vms_warehouse");
            $numberOfStockRecords = $db->getValue("SELECT COUNT(*) FROM ps_vms_stock");

            self::$_hasWarehouseSupport = $numberOfWarehouses > 0 && $numberOfStockRecords > 0;
        } catch (Throwable $e) {
            self::$_hasWarehouseSupport = false;
        }

        return self::$_hasWarehouseSupport;
    }

    /**
     * Get all warehouses.
     *
     * @return VenditWarehouse[]
     */
    public static function getWarehouses(): array
    {
        $db = Db::getInstance();
        $result = $db->executeS("
            SELECT ps_vms_warehouse.id_vms_warehouse, ps_vms_warehouse.title_vms_warehouse, ps_vms_warehouse.vms_ismainoffice, ps_vms_warehouse.vms_iswarehouse
            FROM ps_vms_warehouse
        ");

        $warehouses = [];
        foreach ($result as $row) {
            $warehouses[(int)$row['id_vms_warehouse']] = new VenditWarehouse(
                (int)$row['id_vms_warehouse'],
                $row['title_vms_warehouse'],
                (bool)$row['vms_ismainoffice'],
                (bool)$row['vms_iswarehouse']
            );
        }
        return $warehouses;
    }

    /**
     * Get all products with stock per warehouse.
     *
     * @return VenditStock[]
     */
    public static function getProductStock(int $productId): array
    {
        $db = Db::getInstance();
        $result = $db->executeS("
            SELECT ps_vms_stock.id_product, ps_vms_stock.id_vms_warehouse, ps_vms_stock.quantity
            FROM ps_vms_stock
            WHERE ps_vms_stock.id_product = $productId 
        ");

        $stocks = [];
        foreach ($result as $row) {
            $stocks[(int)$row['id_vms_warehouse']] = new VenditStock(
                (int)$row['id_product'],
                (int)$row['id_vms_warehouse'],
                (int)$row['quantity']
            );
        }

        return $stocks;
    }
}