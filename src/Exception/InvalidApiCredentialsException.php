<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class InvalidApiCredentialsException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $publicKey, string $secretKey)
 */
class InvalidApiCredentialsException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'The provided API credentials for EffectConnect Marketplaces are invalid (public key: "%s" | secret key: "%s").';
}