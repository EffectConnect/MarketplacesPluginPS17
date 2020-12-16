<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException;
    use EffectConnect\PHPSdk\Core\Helper\Reflector;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class OrderUpdate
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderUpdate extends ApiModel implements ApiModelInterface
    {
        const TYPE_CONNECTION_IDENTIFIER    = 'connectionIdentifier';
        const TYPE_CONNECTION_INVOICE       = 'connectionInvoice';
        const TYPE_CONNECTION_NUMBER        = 'connectionNumber';
        const TYPE_EFFECTCONNECT_IDENTIFIER = 'effectConnectIdentifier';
        const TYPE_EFFECTCONNECT_NUMBER     = 'effectConnectNumber';
        const TYPE_CHANNEL_IDENTIFIER       = 'channelIdentifier';
        const TYPE_CHANNEL_NUMBER           = 'channelNumber';

        /**
         * REQUIRED
         *
         * @var string $_orderIdentifierType
         *
         * This type is used to identify the order you're trying to update.
         */
        protected $_orderIdentifierType;

        /**
         * REQUIRED
         *
         * @var string $_orderIdentifier
         */
        protected $_orderIdentifier;

        /**
         * @var int $_connectionIdentifier
         *
         * Use this field to assign an identifier to your order
         */
        protected $_connectionIdentifier;

        /**
         * @var string $_connectionInvoice
         *
         * Use this field to assign an invoice identifier to your order
         */
        protected $_connectionInvoice;

        /**
         * @var string $_connectionNumber
         *
         * Use this field to assign a number to your order
         */
        protected $_connectionNumber;
        /**
         * @var array $_addTags
         *
         * A list of tags to add to this order
         */
        protected $_addTags    = [];
        /**
         * @var array $_removeTags
         *
         * A list of tags to remove from this order
         */
        protected $_removeTags = [];

        public function getName()
        {
            return 'orderUpdate';
        }

        /**
         * @param $identifierType
         *
         * @return OrderUpdate
         *
         * @throws InvalidPropertyValueException
         * @throws \Exception
         */
        public function setOrderIdentifierType($identifierType)
        {
            if (!Reflector::isValid(OrderUpdate::class, $identifierType))
            {
                throw new InvalidPropertyValueException('identifierType');
            }
            $this->_orderIdentifierType = $identifierType;

            return $this;
        }

        public function setOrderIdentifier($identifier)
        {
            $this->_orderIdentifier = $identifier;

            return $this;
        }

        /**
         * @param $tag
         *
         * @return OrderUpdate
         */
        public function addTag($tag)
        {
            $this->_addTags['tag'][] = $tag;

            return $this;
        }

        /**
         * @param $tag
         *
         * @return OrderUpdate
         */
        public function removeTag($tag)
        {
            $this->_removeTags['tag'][] = $tag;

            return $this;
        }

        /**
         * @param int $connectionIdentifier
         *
         * @return OrderUpdate
         */
        public function setConnectionIdentifier($connectionIdentifier)
        {
            $this->_connectionIdentifier = $connectionIdentifier;

            return $this;
        }

        /**
         * @param string $connectionInvoice
         *
         * @return OrderUpdate
         */
        public function setConnectionInvoice($connectionInvoice)
        {
            $this->_connectionInvoice = $connectionInvoice;

            return $this;
        }

        /**
         * @param string $connectionNumber
         *
         * @return OrderUpdate
         */
        public function setConnectionNumber($connectionNumber)
        {
            $this->_connectionNumber = $connectionNumber;

            return $this;
        }

        /**
         * @return string
         */
        public function getOrderIdentifierType()
        {
            return $this->_orderIdentifierType;
        }

        /**
         * @return string
         */
        public function getOrderIdentifier()
        {
            return $this->_orderIdentifier;
        }

        /**
         * @return array
         */
        public function getAddTags()
        {
            return $this->_addTags;
        }

        /**
         * @return array
         */
        public function getRemoveTags()
        {
            return $this->_removeTags;
        }

        /**
         * @return int
         */
        public function getConnectionIdentifier()
        {
            return $this->_connectionIdentifier;
        }

        /**
         * @return string
         */
        public function getConnectionInvoice()
        {
            return $this->_connectionInvoice;
        }

        /**
         * @return string
         */
        public function getConnectionNumber()
        {
            return $this->_connectionNumber;
        }

        protected function isIterator()
        {
            return true;
        }
    }
