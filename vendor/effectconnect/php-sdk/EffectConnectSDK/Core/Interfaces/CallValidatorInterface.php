<?php
    namespace EffectConnect\PHPSdk\Core\Interfaces;

    use EffectConnect\PHPSdk\Core\Exception\InvalidPayloadException;

    /**
     * Interface CallValidatorInterface
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    interface CallValidatorInterface
    {
        /**
         * @param string $callAction
         */
        public function setup($callAction);

        /**
         * @param $argument
         *
         * @return bool
         *
         * @throws InvalidPayloadException
         */
        public function validateCall($argument);
    }