<?php
    namespace EffectConnect\PHPSdk\Core\Exception;
    
    /**
     * Class InvalidPropertyValueException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidPropertyValueException extends \Exception
    {
        public function __construct($property)
        {
            parent::__construct('Invalid value for property `'.$property.'`.');
        }
    }