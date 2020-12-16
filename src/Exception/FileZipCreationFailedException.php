<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class FileZipCreationFailedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $message)
 */
class FileZipCreationFailedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = '%s';
}