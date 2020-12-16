<?php

namespace EffectConnect\Marketplaces\Enums;

/**
 * Class InvalidEAN
 * @package EffectConnect\Marketplaces\Enums
 */
class InvalidEAN
{
    /**
     * Export product with invalid EAN (without its EAN).
     */
    const PRODUCT_EXPORT_WITHOUT_EAN = 0;

    /**
     * Skip export of product with invalid EAN.
     */
    const PRODUCT_EXPORT_SKIP = 1;
}