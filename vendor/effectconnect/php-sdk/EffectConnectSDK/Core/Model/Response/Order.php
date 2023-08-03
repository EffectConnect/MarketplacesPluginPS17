<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class Order
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class Order
    {
        /**
         * @var OrderIdentifiers $_identifiers
         */
        private $_identifiers;
        /**
         * @var ChannelInfo $_channelInfo
         */
        private $_channelInfo;
        /**
         * @var string $_currency
         */
        private $_currency;
        /**
         * @var string $_isTaxShifted
         */
        private $_isTaxShifted;
        /**
         * @var \DateTime $_date
         */
        private $_date;
        /**
         * @var string $_status
         */
        private $_status;
        /**
         * @var BillingAddress $_billingAddress
         */
        private $_billingAddress;
        /**
         * @var ShippingAddress $_shippingAddress
         */
        private $_shippingAddress;
        /**
         * @var Line[] $_lines
         */
        private $_lines             = [];
        /**
         * @var Notification[] $_notifications
         */
        private $_notifications     = [];
        /**
         * @var OrderFee[] $_fees
         */
        private $_fees              = [];
        /**
         * @var Tag[] $_tags
         */
        private $_tags              = [];

        /**
         * OrderReadResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_identifiers     = new OrderIdentifiers(Payload::extract($payload, 'identifiers'));
            $this->_channelInfo     = new ChannelInfo(Payload::extract($payload, 'channelInfo'));
            $this->_currency        = Payload::extract($payload, 'currency');
            $this->_isTaxShifted    = Payload::extract($payload, 'isTaxShifted');
            $this->_date            = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'date'));
            $this->_status          = Payload::extract($payload, 'status');
            $this->_billingAddress  = new BillingAddress(Payload::extract($payload, 'billingAddress'));
            $this->_shippingAddress = (new ShippingAddress(Payload::extract($payload, 'shippingAddress')));
            foreach (Payload::extract($payload, 'lines', true) as $line)
            {
                $this->_lines[] = new Line($line);
            }
            if (Payload::contains($payload, 'notifications'))
            {
                foreach (Payload::extract($payload, 'notifications', true) as $notification)
                {
                    $this->_notifications[] = new Notification($notification);
                }
            }
            if (Payload::contains($payload, 'fees'))
            {
                foreach (Payload::extract($payload, 'fees', true) as $fee)
                {
                    $this->_fees[] = new OrderFee(Payload::extract($fee, 'fee', true));
                }
            }
            if (Payload::contains($payload, 'tags'))
            {
                foreach (Payload::extract($payload, 'tags', true) as $tag)
                {
                    $this->_tags[] = new Tag((string)$tag);
                }
            }
        }

        /**
         * @return OrderIdentifiers
         */
        public function getIdentifiers()
        {
            return $this->_identifiers;
        }

        /**
         * @return ChannelInfo
         */
        public function getChannelInfo()
        {
            return $this->_channelInfo;
        }

        /**
         * @return string
         */
        public function getCurrency()
        {
            return $this->_currency;
        }

        /**
         * @return string
         */
        public function getIsTaxShifted()
        {
            return $this->_isTaxShifted;
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
         * @return BillingAddress
         */
        public function getBillingAddress()
        {
            return $this->_billingAddress;
        }

        /**
         * @return ShippingAddress
         */
        public function getShippingAddress()
        {
            return $this->_shippingAddress;
        }

        /**
         * @return Line[]
         */
        public function getLines()
        {
            return $this->_lines;
        }

        /**
         * @return Notification[]
         */
        public function getNotifications()
        {
            return $this->_notifications;
        }

        /**
         * @return OrderFee[]
         */
        public function getFees()
        {
            return $this->_fees;
        }

        /**
         * @return Tag[]
         */
        public function getTags()
        {
            return $this->_tags;
        }
    }
