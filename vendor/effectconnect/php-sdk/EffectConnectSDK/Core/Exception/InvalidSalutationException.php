<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidSalutationException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidSalutationException extends \Exception
    {
        public function __construct()
        {
            parent::__construct('Incorrect salutation.');
        }
    }