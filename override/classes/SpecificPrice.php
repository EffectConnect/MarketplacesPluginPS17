<?php

/**
 * Class SpecificPrice
 */
class SpecificPrice extends SpecificPriceCore
{
    /**
     * @param false $null_values
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($null_values = false)
    {
        $exception = null;

        $specificPriceOld = new SpecificPrice($this->id);
        try {
            $return = parent::update($null_values);
        } catch (Exception $exception) {}
        $specificPriceNew = new SpecificPrice($this->id);

        if ($this->specificPriceHasChanged($specificPriceOld, $specificPriceNew)) {
            Hook::exec(
                'ecOfferExportQueueSchedule',
                [
                    'id_product' => $specificPriceOld->id_product
                ]
            );
            if ($specificPriceOld->id_product != $specificPriceNew->id_product) {
                Hook::exec(
                    'ecOfferExportQueueSchedule',
                    [
                        'id_product' => $specificPriceNew->id_product
                    ]
                );
            }
        }

        if ($exception instanceof Exception) {
            throw $exception;
        }

        return $return;
    }

    /**
     * @param bool $autodate
     * @param false $nullValues
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autodate = true, $nullValues = false)
    {
        $exception = null;

        try {
            $return = parent::add($autodate, $nullValues);
        } catch (Exception $exception) {}
        $specificPriceNew = new SpecificPrice($this->id);

        if ($this->specificPriceHasChanged(null, $specificPriceNew)) {
            Hook::exec(
                'ecOfferExportQueueSchedule',
                [
                    'id_product' => $specificPriceNew->id_product
                ]
            );
        }

        if ($exception instanceof Exception) {
            throw $exception;
        }

        return $return;
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function delete()
    {
        $exception = null;

        $specificPriceOld = new SpecificPrice($this->id);
        try {
            $return = parent::delete();
        } catch (Exception $exception) {}

        if ($this->specificPriceHasChanged($specificPriceOld, null)) {
            Hook::exec(
                'ecOfferExportQueueSchedule',
                [
                    'id_product' => $specificPriceOld->id_product
                ]
            );
        }

        if ($exception instanceof Exception) {
            throw $exception;
        }

        return $return;
    }

    /**
     * For now let's send hook to EC for every changes that touches a product and not a cart specific rule.
     * Could be improved in future versions.
     *
     * @param SpecificPrice|null $specificPriceOld
     * @param SpecificPrice|null $specificPriceNew
     * @return bool
     */
    protected function specificPriceHasChanged(SpecificPrice $specificPriceOld = null, SpecificPrice $specificPriceNew = null)
    {
        // Add
        if (is_null($specificPriceOld) && !is_null($specificPriceNew))
        {
            if ($specificPriceNew->id_product > 0 && $specificPriceNew->id_cart == 0) {
                return true;
            }
        }
        // Delete
        elseif (!is_null($specificPriceOld) && is_null($specificPriceNew))
        {
            if ($specificPriceOld->id_product > 0 && $specificPriceOld->id_cart == 0) {
                return true;
            }
        }
        // Update
        elseif (!is_null($specificPriceOld) && !is_null($specificPriceNew))
        {
            if ($specificPriceOld->id_product > 0 && $specificPriceOld->id_cart == 0
                && $specificPriceNew->id_product > 0 && $specificPriceNew->id_cart == 0) {
                return true;
            }
        }
        return false;
    }
}
