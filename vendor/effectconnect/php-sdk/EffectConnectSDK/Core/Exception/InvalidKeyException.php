<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidKeyException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidKeyException extends \Exception
    {
        public function __construct($type)
        {
            parent::__construct('Incorrect key given for type `'.$type.'`.');
        }
    }