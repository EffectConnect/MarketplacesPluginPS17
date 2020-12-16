<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class StatusHistory
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class StatusHistory
    {
        /**
         * @var string $_status
         */
        private $_status;
        /**
         * @var int $_items
         */
        private $_items;
        /**
         * @var int $_processed
         */
        private $_processed;
        /**
         * @var int $_succeeded
         */
        private $_succeeded;
        /**
         * @var int $_errors
         */
        private $_errors;
        /**
         * @var \DateTime $_date
         */
        private $_date;

        /**
         * StatusHistory constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_status    = Payload::extract($payload, 'status');
            $this->_items     = (int)Payload::extract($payload, 'items');
            $this->_processed = (int)Payload::extract($payload, 'processed');
            $this->_succeeded = (int)Payload::extract($payload, 'succeeded');
            $this->_errors    = (int)Payload::extract($payload, 'errors');
            $this->_date      = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'date'));
        }

        /**
         * @return string
         */
        public function getStatus()
        {
            return $this->_status;
        }

        /**
         * @return int
         */
        public function getItems()
        {
            return $this->_items;
        }

        /**
         * @return int
         */
        public function getProcessed()
        {
            return $this->_processed;
        }

        /**
         * @return int
         */
        public function getSucceeded()
        {
            return $this->_succeeded;
        }

        /**
         * @return int
         */
        public function getErrors()
        {
            return $this->_errors;
        }

        /**
         * @return \DateTime
         */
        public function getDate()
        {
            return $this->_date;
        }
    }