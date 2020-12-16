<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class OfferExportFailedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $connectionId, string $message)
 */
class OfferExportFailedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'The offer export for connection %s failed with message: %s';
}