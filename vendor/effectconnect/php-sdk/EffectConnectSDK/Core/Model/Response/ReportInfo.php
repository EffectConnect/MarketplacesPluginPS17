<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class ReportInfo
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ReportInfo
    {
        /**
         * @var string $_processId
         */
        private $_processId;
        /**
         * @var \DateTime $_createdAt
         */
        private $_createdAt;
        /**
         * @var string $_processType
         */
        private $_processType;

        /**
         * ReportInfo constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_processId   = Payload::extract($payload, 'processId');
            $this->_createdAt   = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'createdAt'));
            $this->_processType = Payload::extract($payload, 'processType');
        }

        /**
         * @return string
         */
        public function getProcessId()
        {
            return $this->_processId;
        }

        /**
         * @return \DateTime
         */
        public function getCreatedAt()
        {
            return $this->_createdAt;
        }

        /**
         * @return string
         */
        public function getProcessType()
        {
            return $this->_processType;
        }
    }