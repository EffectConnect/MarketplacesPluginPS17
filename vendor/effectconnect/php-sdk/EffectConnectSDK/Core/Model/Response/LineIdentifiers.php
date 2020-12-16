<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class LineIdentifiers
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class LineIdentifiers
    {
        /**
         * @var string $_connectionLineId
         */
        private $_connectionLineId;
        /**
         * @var string $_effectConnectLineId
         */
        private $_effectConnectLineId;
        /**
         * @var string $_channelLineId
         */
        private $_channelLineId;
        /**
         * @var string $_effectConnectId
         */
        private $_effectConnectId;

        /**
         * LineIdentifiers constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_connectionLineId    = Payload::extract($payload, 'connectionLineId');
            $this->_effectConnectLineId = Payload::extract($payload, 'effectConnectLineId');
            $this->_channelLineId       = Payload::extract($payload, 'channelLineId');
            $this->_effectConnectId     = Payload::extract($payload, 'effectConnectId');
        }

        /**
         * @return string
         */
        public function getConnectionLineId()
        {
            return $this->_connectionLineId;
        }

        /**
         * @return string
         */
        public function getEffectConnectLineId()
        {
            return $this->_effectConnectLineId;
        }

        /**
         * @return string
         */
        public function getChannelLineId()
        {
            return $this->_channelLineId;
        }

        /**
         * @return string
         */
        public function getEffectConnectId()
        {
            return $this->_effectConnectId;
        }
    }