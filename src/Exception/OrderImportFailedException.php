<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class OrderImportFailedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $connectionId, string $message)
 */
class OrderImportFailedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'The order import for connection %s failed with message: %s';
}