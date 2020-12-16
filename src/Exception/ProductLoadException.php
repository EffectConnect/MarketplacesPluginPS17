<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class ProductLoadException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $reason)
 */
class ProductLoadException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'Product could not be loaded (reason: %s).';
}