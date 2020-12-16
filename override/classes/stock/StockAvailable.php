<?php

/**
 * Class StockAvailable
 */
class StockAvailable extends StockAvailableCore
{
    /**
     * @param $id_product
     * @param $id_product_attribute
     * @param $quantity
     * @param null $id_shop
     * @param bool $add_movement
     * @return bool|void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function setQuantity($id_product, $id_product_attribute, $quantity, $id_shop = null, $add_movement = true)
    {
        $exception = null;

        $idStockAvailableOld = intval(StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop));
        $stockAvailableOld   = new StockAvailable($idStockAvailableOld);

        try {
            parent::setQuantity($id_product, $id_product_attribute, $quantity, $id_shop, $add_movement);
        } catch (Exception $exception) {}

        $idStockAvailableNew = intval(StockAvailable::getStockAvailableIdByProductId($id_product, $id_product_attribute, $id_shop));
        $stockAvailableNew   = new StockAvailable($idStockAvailableNew);

        if ($stockAvailableOld->quantity != $stockAvailableNew->quantity) {
            Hook::exec(
                'ecOfferExportQueueSchedule',
                [
                    'id_product' => $stockAvailableNew->id_product
                ]
            );
        }

        if ($exception instanceof Exception) {
            throw $exception;
        }
    }
}
