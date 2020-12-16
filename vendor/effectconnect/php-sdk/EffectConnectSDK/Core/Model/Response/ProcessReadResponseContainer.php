<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class ProcessReadResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ProcessReadResponseContainer implements ResponseContainerInterface
    {
        /**
         * @var \DateTime $_createdAt
         */
        private $_createdAt;
        /**
         * @var string $_process
         */
        private $_process;
        /**
         * @var bool $_hasReport
         */
        private $_hasReport;
        /**
         * @var string $_currentStatus
         */
        private $_currentStatus;
        /**
         * @var \DateTime $_startedAt
         */
        private $_startedAt;
        /**
         * @var string $_type
         */
        private $_type;
        /**
         * @var StatusHistory[] $_statusHistory
         */
        private $_statusHistory = [];

        /**
         * ProcessReadResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_createdAt       = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'createdAt'));
            $this->_process         = Payload::extract($payload, 'process');
            $this->_hasReport       = (int)Payload::extract($payload, 'hasReport')===1;
            $this->_currentStatus   = Payload::extract($payload, 'currentStatus');
            $this->_startedAt       = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'startedAt'));
            $this->_type            = Payload::extract($payload, 'type');
            foreach (Payload::extract($payload, 'statusHistory', true) as $status)
            {
                $this->_statusHistory[] = new StatusHistory($status);
            }
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
        public function getProcess()
        {
            return $this->_process;
        }

        /**
         * @return bool
         */
        public function isHasReport()
        {
            return $this->_hasReport;
        }

        /**
         * @return string
         */
        public function getCurrentStatus()
        {
            return $this->_currentStatus;
        }

        /**
         * @return \DateTime
         */
        public function getStartedAt()
        {
            return $this->_startedAt;
        }

        /**
         * @return string
         */
        public function getType()
        {
            return $this->_type;
        }

        /**
         * @return StatusHistory[]
         */
        public function getStatusHistory()
        {
            return $this->_statusHistory;
        }
    }