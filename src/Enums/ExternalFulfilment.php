<?php

namespace EffectConnect\Marketplaces\Enums;

/**
 * Class ExternalFulfilment
 * @package EffectConnect\Marketplaces\Enums
 */
class ExternalFulfilment
{
    /**
     * Only import my own orders.
     */
    const INTERNAL_ORDERS = 'internal_orders';

    /**
     * Only import orders that are fulfilled externally by the channel.
     */
    const EXTERNAL_ORDERS = 'external_orders';

    /**
     * Import both my own order and externally fulfilled orders.
     */
    const EXTERNAL_AND_INTERNAL_ORDERS = 'external_and_internal_orders';
}