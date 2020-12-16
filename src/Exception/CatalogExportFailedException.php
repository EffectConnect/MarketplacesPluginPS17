<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class CatalogExportFailedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $connectionId, string $message)
 */
class CatalogExportFailedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'The catalog export for connection %s failed with message: %s';
}