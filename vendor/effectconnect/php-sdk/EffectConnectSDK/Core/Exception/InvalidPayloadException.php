<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidPayloadException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidPayloadException extends \Exception
    {
        public function __construct($action)
        {
            parent::__construct('Invalid payload for action `'.$action.'`.');
        }
    }