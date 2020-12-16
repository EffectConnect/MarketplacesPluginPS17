<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class LineStatusHistory
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class LineStatusHistory
    {
        /**
         * @var \DateTime $_date
         */
        private $_date;
        /**
         * @var string $_status
         */
        private $_status;
        /**
         * @var string $_trackingCode
         */
        private $_trackingCode;
        /**
         * @var string $_trackingUrl
         */
        private $_trackingUrl;
        /**
         * @var string $_carrier
         */
        private $_carrier;

        /**
         * LineStatusHistory constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_date         = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'date'));
            $this->_status       = Payload::extract($payload, 'status');
            $this->_trackingCode = Payload::extract($payload, 'trackingCode');
            $this->_trackingUrl  = Payload::extract($payload, 'trackingUrl');
            $this->_carrier      = Payload::extract($payload, 'carrier');
        }

        /**
         * @return \DateTime
         */
        public function getDate()
        {
            return $this->_date;
        }

        /**
         * @return string
         */
        public function getStatus()
        {
            return $this->_status;
        }

        /**
         * @return string
         */
        public function getTrackingCode()
        {
            return $this->_trackingCode;
        }

        /**
         * @return string
         */
        public function getTrackingUrl()
        {
            return $this->_trackingUrl;
        }

        /**
         * @return string
         */
        public function getCarrier()
        {
            return $this->_carrier;
        }
    }