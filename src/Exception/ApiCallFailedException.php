<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class ApiCallFailedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $message)
 */
class ApiCallFailedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'EffectConnect API call failed with message(s): %s.';
}