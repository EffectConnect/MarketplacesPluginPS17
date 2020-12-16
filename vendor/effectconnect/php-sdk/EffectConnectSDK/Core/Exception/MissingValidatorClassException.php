<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class MissingValidatorClassException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class MissingValidatorClassException extends \Exception
    {
        public function __construct()
        {
            parent::__construct('No validator class set.');
        }
    }