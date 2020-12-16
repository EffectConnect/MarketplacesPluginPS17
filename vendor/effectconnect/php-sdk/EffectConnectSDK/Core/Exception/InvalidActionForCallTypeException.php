<?php
    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class InvalidActionForCallTypeException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class InvalidActionForCallTypeException extends \Exception
    {
        public function __construct()
        {
            parent::__construct('This action is not permitted for this calltype.');
        }
    }