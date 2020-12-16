<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidValidatorClassException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidValidatorClassException extends \Exception
    {
        public function __construct()
        {
            parent::__construct('Invalid validator class.');
        }
    }