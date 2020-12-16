<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException;
    use EffectConnect\PHPSdk\Core\Helper\Reflector;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class OrderLineUpdate
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderLineUpdate extends ApiModel implements ApiModelInterface
    {
        const TYPE_CONNECTION_LINE_ID    = 'connectionLineId';
        const TYPE_EFFECTCONNECT_LINE_ID = 'effectConnectLineId';
        const TYPE_EFFECTCONNECT_ID      = 'effectConnectId';
        const TYPE_CHANNEL_LINE_ID       = 'channelLineId';

        /**
         * REQUIRED
         *
         * @var string $_orderlineIdentifierType
         *
         * This type is used to identify the orderline you're trying to update.
         */
        protected $_orderlineIdentifierType;

        /**
         * REQUIRED
         *
         * @var string $_orderlineIdentifier
         */
        protected $_orderlineIdentifier;

        /**
         * OPTIONAL
         * @var string $_trackingNumber
         */
        protected $_trackingNumber;

        /**
         * OPTIONAL
         * @var string $_trackingUrl
         */
        protected $_trackingUrl;

        /**
         * OPTIONAL
         * @var string $_carrier
         */
        protected $_carrier;

        /**
         * OPTIONAL
         * @var \DateTime $_expectedDelivery
         */
        protected $_expectedDelivery;

        public function getName()
        {
            return 'lineUpdate';
        }

        /**
         * @return string
         */
        public function getOrderlineIdentifierType()
        {
            return $this->_orderlineIdentifierType;
        }

        /**
         * @param $identifierType
         *
         * @return OrderLineUpdate
         *
         * @throws InvalidPropertyValueException
         * @throws \Exception
         */
        public function setOrderlineIdentifierType($identifierType)
        {
            if (!Reflector::isValid(OrderLineUpdate::class, $identifierType))
            {
                throw new InvalidPropertyValueException('identifierType');
            }
            $this->_orderlineIdentifierType = $identifierType;

            return $this;
        }

        /**
         * @return string
         */
        public function getOrderlineIdentifier()
        {
            return $this->_orderlineIdentifier;
        }

        /**
         * @param string $identifier
         *
         * @return OrderLineUpdate
         */
        public function setOrderlineIdentifier($identifier)
        {
            $this->_orderlineIdentifier = $identifier;

            return $this;
        }

        /**
         * @return string
         */
        public function getTrackingNumber()
        {
            return $this->_trackingNumber;
        }

        /**
         * @param string $trackingNumber|null
         *
         * @return OrderLineUpdate
         */
        public function setTrackingNumber($trackingNumber)
        {
            $this->_trackingNumber = $trackingNumber;

            return $this;
        }

        /**
         * @return string
         */
        public function getTrackingUrl()
        {
            return $this->_trackingUrl;
        }

        /**
         * @param string $trackingUrl|null
         *
         * @return OrderLineUpdate
         */
        public function setTrackingUrl($trackingUrl)
        {
            $this->_trackingUrl = $trackingUrl;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getCarrier()
        {
            return $this->_carrier;
        }

        /**
         * @param string $carrier
         *
         * @return OrderLineUpdate
         */
        public function setCarrier($carrier)
        {
            $this->_carrier = $carrier;

            return $this;
        }

        /**
         * @return string
         */
        public function getExpectedDelivery()
        {
            if ($this->_expectedDelivery)
            {
                return $this->_expectedDelivery->format('Y-m-d\TH:i:sP');
            }

            return null;
        }

        /**
         * @param \DateTime $expectedDelivery
         *
         * @return OrderLineUpdate
         */
        public function setExpectedDelivery(\DateTime $expectedDelivery)
        {
            $this->_expectedDelivery = $expectedDelivery;

            return $this;
        }
    }