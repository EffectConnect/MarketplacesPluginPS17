<?php

namespace EffectConnect\Marketplaces\Service;

use Address;
use Carrier;
use Country;
use Validate;
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

            foreach ($order->getLines() as $line) {
                foreach ($line->getFees() as $fee) {
                    if ($fee->getType() !== FeeType::COMMISSION) {
                        $feeAmount += $fee->getAmount(); // TODO: currency conversion? $sourceCurrency->convert($fee->getAmount(), $destinationCurrency);
                    }
                }
            }

            if ($useTax) {
                $feeAmountExcludingTax = $feeAmount;
            } else {
                $carrier               = new Carrier($idCarrier);
                $effectConnectAddress  = $order->getShippingAddress();
                $address               = new Address();
                $address->id_customer  = 0;
                $address->firstname    = $effectConnectAddress->getFirstName();
                $address->lastname     = $effectConnectAddress->getLastName();
                $address->company      = $effectConnectAddress->getCompany();
                $address->postcode     = $effectConnectAddress->getZipCode();
                $address->phone        = $effectConnectAddress->getPhone();
                $address->vat_number   = $effectConnectAddress->getTaxNumber();
                $address->city         = $effectConnectAddress->getCity();
                $address->country      = $effectConnectAddress->getCountry();
                if (Validate::isLanguageIsoCode($effectConnectAddress->getCountry())) {
                    $idCountry = Country::getByIso($effectConnectAddress->getCountry());
                    if ($idCountry) {
                        $address->id_country = $idCountry;
                    }
                }
                $carrierTaxRate        = $carrier->getTaxesRate($address);
                $feeAmountExcludingTax = $feeAmount / (100 + $carrierTaxRate) * 100;
            }
        }
        return floatval($feeAmountExcludingTax);
    }
}