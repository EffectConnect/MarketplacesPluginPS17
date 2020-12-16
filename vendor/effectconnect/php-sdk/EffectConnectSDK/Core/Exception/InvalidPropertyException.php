<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidPropertyException
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidPropertyException extends \Exception
    {
        public function __construct()
        {
            parent::__construct('Invalid property set.');
        }
    }