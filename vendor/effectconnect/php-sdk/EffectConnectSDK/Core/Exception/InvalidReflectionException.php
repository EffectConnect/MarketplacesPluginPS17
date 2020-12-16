<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidPropertyValueException
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidReflectionException extends \Exception
    {
        public function __construct()
        {
            parent::__construct('Invalid predefined value.');
        }
    }