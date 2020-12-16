<?php

namespace EffectConnect\Marketplaces\Exception;

use Exception;

/**
 * Class AbstractException
 * @package EffectConnect\Marketplaces\Exception
 */
abstract class AbstractException extends Exception
{
    /**
     * The message format for the error (used for sprintf formatting).
     */
    protected const MESSAGE_FORMAT = '';

    /**
     * The error code for the error.
     */
    protected const ERROR_CODE= 0;

    /**
     * AbstractException constructor.
     *
     * @param mixed ...$parameters
     */
    public function __construct(...$parameters)
    {
        $message = sprintf(static::MESSAGE_FORMAT, ...$parameters);

        parent::__construct($message, static::ERROR_CODE, null);
    }
}