<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class Request
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class Request
    {
        /**
         * @var string $_requestType
         */
        private $_requestType;
        /**
         * @var string $_requestAction
         */
        private $_requestAction;
        /**
         * @var string $_requestVersion
         */
        private $_requestVersion;
        /**
         * @var string $_requestIdentifier
         */
        private $_requestIdentifier;
        /**
         * @var \DateTime $_processedAt
         */
        private $_processedAt;

        /**
         * Request constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_requestType       = Payload::extract($payload, 'RequestType');
            $this->_requestAction     = Payload::extract($payload, 'RequestAction');
            $this->_requestVersion    = Payload::extract($payload, 'RequestVersion');
            $this->_requestIdentifier = Payload::extract($payload, 'RequestIdentifier');
            $this->_processedAt       = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'ProcessedAt'));
        }

        /**
         * @return string
         */
        public function getRequestType()
        {
            return $this->_requestType;
        }

        /**
         * @return string
         */
        public function getRequestAction()
        {
            return $this->_requestAction;
        }

        /**
         * @return string
         */
        public function getRequestVersion()
        {
            return $this->_requestVersion;
        }

        /**
         * @return string
         */
        public function getRequestIdentifier()
        {
            return $this->_requestIdentifier;
        }

        /**
         * @return \DateTime
         */
        public function getProcessedAt()
        {
            return $this->_processedAt;
        }
    }