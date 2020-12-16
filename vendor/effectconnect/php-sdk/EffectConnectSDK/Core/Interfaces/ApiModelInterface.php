<?php
    namespace EffectConnect\PHPSdk\Core\Interfaces;

    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyException;

    /**
     * Interface ApiModelInterface
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    interface ApiModelInterface
    {
        /**
         * @return string
         *
         * Returns the xml root node
         */
        public function getName();

        /**
         * @return string
         *
         * @throws InvalidPropertyException
         */
        public function getXml();
    }