<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class UnknownException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $message)
 */
class UnknownException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'An unknown exception occurred: %s';
}