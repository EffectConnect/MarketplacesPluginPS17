<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class InitContextFailedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct(string $message)
 */
class InitContextFailedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'Initiating of context failed (%s).';
}