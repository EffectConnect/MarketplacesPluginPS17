<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidApiCallException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidApiCallException extends \Exception
    {
        public function __construct($call)
        {
            parent::__construct('The api type `'.$call.'` does not exist.');
        }
    }