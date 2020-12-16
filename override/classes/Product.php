<?php

/**
 * Class Product
 */
class Product extends ProductCore
{
    /**
     * @param int $id_product_attribute
     * @param float $wholesale_price
     * @param float $price
     * @param float $weight
     * @param float $unit
     * @param float $ecotax
     * @param $id_images
     * @param string $reference
     * @param string $ean13
     * @param int $default
     * @param null $location
     * @param null $upc
     * @param null $minimal_quantity
     * @param null $available_date
     * @param bool $update_all_fields
     * @param array $id_shop_list
     * @param string $isbn
     * @param null $low_stock_threshold
     * @param false $low_stock_alert
     * @param null $mpn
     * @return array|bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function updateAttribute(
        $id_product_attribute,
        $wholesale_price,
        $price,
        $weight,
        $unit,
        $ecotax,
        $id_images,
        $reference,
        $ean13,
        $default,
        $location = null,
        $upc = null,
        $minimal_quantity = null,
        $available_date = null,
        $update_all_fields = true,
        array $id_shop_list = array(),
        $isbn = '',
        $low_stock_threshold = null,
        $low_stock_alert = false,
        $mpn = null
    ) {
        $exception = null;

        $combinationOld = new Combination($id_product_attribute);
        try {
            $return = parent::updateAttribute(
                $id_product_attribute,
                $wholesale_price,
                $price,
                $weight,
                $unit,
                $ecotax,
                $id_images,
                $reference,
                $ean13,
                $default,
                $location,
                $upc,
                $minimal_quantity,
                $available_date,
                $update_all_fields,
                $id_shop_list,
                $isbn,
                $low_stock_threshold,
                $low_stock_alert,
                $mpn
            );
        } catch (Exception $exception) {}
        $combinationNew = new Combination($id_product_attribute);

        if ($combinationOld->price != $combinationNew->price) {
            Hook::exec(
                'ecOfferExportQueueSchedule',
                [
                    'id_product' => $this->id
                ]
            );
        }

        if ($exception instanceof Exception) {
            throw $exception;
        }

        return $return;
    }

    /**
     * @param false $null_values
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($null_values = false)
    {
        $exception = null;

        $productOld = new Product($this->id);
        try {
            $return = parent::update($null_values);
        } catch (Exception $exception) {}
        $productNew = new Product($this->id);

        if ($productOld->price != $productNew->price) {
            Hook::exec(
                'ecOfferExportQueueSchedule',
                [
                    'id_product' => $this->id
                ]
            );
        }

        if ($exception instanceof Exception) {
            throw $exception;
        }

        return $return;
    }
}
