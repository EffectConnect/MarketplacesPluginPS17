<?php

namespace EffectConnect\Marketplaces\Service;

use Carrier;
use EffectConnect\Marketplaces\Enums\FeeType;
use EffectConnect\PHPSdk\Core\Model\Response\Order as EffectConnectOrder;

/**
 * Class ShippingCostCalculator
 * @package EffectConnect\Marketplaces\Service
 */
class ShippingCostCalculator
{
    /**
     * @param EffectConnectOrder $order
     * @param int|null $idCarrier
     * @param bool $useTax
     * @return float
     */
    public static function calculate(EffectConnectOrder $order, int $idCarrier = null, bool $useTax = true)
    {
        $feeAmount = $feeAmountExcludingTax = 0;
        if ($idCarrier > 0)
        {
            foreach ($order->getFees() as $fee)
            {
                if ($fee->getType() !== FeeType::COMMISSION) {
                    $feeAmount += $fee->getAmount(); // TODO: currency conversion? $sourceCurrency->convert($fee->getAmount(), $destinationCurrency);
                }
            }
            if ($useTax) {
                $feeAmountExcludingTax = $feeAmount;
            } else {
                $carrier               = new Carrier($idCarrier);
                $carrierTaxRate        = $carrier->getTaxesRate(); // TODO: use address ID?
                $feeAmountExcludingTax = $feeAmount / (100 + $carrierTaxRate) * 100;
            }
        }
        return floatval($feeAmountExcludingTax);
    }
}