<?php

use EffectConnect\Marketplaces\Service\ShippingCostCalculator;
use EffectConnect\PHPSdk\Core\Model\Response\Order as EffectConnectOrder;

/**
 * Class Cart
 */
class Cart extends CartCore
{
    /**
     * @var EffectConnectOrder|null
     */
    protected static $_effectConnectOrder = null;

    /**
     * @param EffectConnectOrder $order
     */
    public static function setEffectConnectOrder(EffectConnectOrder $order)
    {
        // TODO:
        //  Can we prevent the use of static var to link the EffectConnectOrder to a specific cart?
        //  When importing orders the PaymentModule->createOrderFromCart will reload the cart from database!
        self::$_effectConnectOrder = $order;
    }

    /**
     * @return EffectConnectOrder|null
     */
    public function getEffectConnectOrder()
    {
        return self::$_effectConnectOrder;
    }

    /**
     * @param null $id_carrier
     * @param bool $use_tax
     * @param Country|null $default_country
     * @param null $product_list
     * @param null $id_zone
     * @param bool $keepOrderPrices
     * @return bool|float
     */
    public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null, bool $keepOrderPrices = false)
    {
        $ecOrder = self::getEffectConnectOrder();
        if ($ecOrder instanceof EffectConnectOrder) {
            $shippingCost = ShippingCostCalculator::calculate($ecOrder, intval($id_carrier), boolval($use_tax));
            return round($shippingCost, 9); // Without rounding price is not valid accordingly to PS's function 'isPrice'
        }
        return parent::getPackageShippingCost($id_carrier, $use_tax, $default_country, $product_list, $id_zone, $keepOrderPrices);
    }
}