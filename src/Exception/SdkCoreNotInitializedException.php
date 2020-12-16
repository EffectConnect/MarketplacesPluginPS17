<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class SdkCoreNotInitializedException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct()
 */
class SdkCoreNotInitializedException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'Tried to fetch SDK core when it was not initialised yet.';
}