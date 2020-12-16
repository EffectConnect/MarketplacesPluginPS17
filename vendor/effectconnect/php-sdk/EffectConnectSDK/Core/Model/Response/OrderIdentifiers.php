<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class OrderIdentifiers
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderIdentifiers
    {
        /**
         * @var string $_connectionNumber
         */
        private $_connectionNumber;
        /**
         * @var string $_effectConnectNumber
         */
        private $_effectConnectNumber;
        /**
         * @var string $_channelNumber
         */
        private $_channelNumber;

        /**
         * OrderIdentifiers constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_channelNumber       = Payload::extract($payload, 'channelNumber');
            $this->_connectionNumber    = Payload::extract($payload, 'connectionNumber');
            $this->_effectConnectNumber = Payload::extract($payload, 'effectConnectNumber');
        }

        /**
         * @return string
         */
        public function getConnectionNumber()
        {
            return $this->_connectionNumber;
        }

        /**
         * @return string
         */
        public function getEffectConnectNumber()
        {
            return $this->_effectConnectNumber;
        }

        /**
         * @return string
         */
        public function getChannelNumber()
        {
            return $this->_channelNumber;
        }
    }