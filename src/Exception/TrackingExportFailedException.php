<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class TrackingExportFailedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $connectionId, string $message)
 */
class TrackingExportFailedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'The tracking export for connection %s failed with message: %s';
}