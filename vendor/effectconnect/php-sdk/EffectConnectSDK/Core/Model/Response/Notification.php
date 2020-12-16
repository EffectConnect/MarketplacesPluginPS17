<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class Notification
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class Notification
    {
        /**
         * @var string $_type
         */
        private $_type;
        /**
         * @var string $_notification
         */
        private $_notification;
        /**
         * @var \DateTime $_createdAt
         */
        private $_createdAt;

        /**
         * Notification constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_type         = Payload::extract($payload, 'type');
            $this->_notification = Payload::extract($payload, 'notification');
            $this->_createdAt    = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'createdAt'));
        }

        /**
         * @return string
         */
        public function getType()
        {
            return $this->_type;
        }

        /**
         * @return string
         */
        public function getNotification()
        {
            return $this->_notification;
        }

        /**
         * @return \DateTime
         */
        public function getCreatedAt()
        {
            return $this->_createdAt;
        }
    }