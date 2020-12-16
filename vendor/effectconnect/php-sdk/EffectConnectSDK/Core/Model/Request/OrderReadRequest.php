<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException;
    use EffectConnect\PHPSdk\Core\Helper\Reflector;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class OrderReadRequest
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderReadRequest extends ApiModel implements ApiModelInterface
    {
        const TYPE_CONNECTION_IDENTIFIER    = 'connectionIdentifier';
        const TYPE_CONNECTION_INVOICE       = 'connectionInvoice';
        const TYPE_CONNECTION_NUMBER        = 'connectionNumber';
        const TYPE_EFFECTCONNECT_IDENTIFIER = 'effectConnectIdentifier';
        const TYPE_EFFECTCONNECT_NUMBER     = 'effectConnectNumber';
        const TYPE_CHANNEL_IDENTIFIER       = 'channelIdentifier';
        const TYPE_CHANNEL_NUMBER           = 'channelNumber';

        /**
         * @var string $_identifierType
         */
        protected $_identifierType;
        /**
         * @var string $_identifier
         */
        protected $_identifier;

        public function getName()
        {
            return 'order';
        }

        /**
         * @return string
         */
        public function getIdentifierType()
        {
            return $this->_identifierType;
        }

        /**
         * @param $identifierType
         *
         * @return OrderReadRequest
         *
         * @throws InvalidPropertyValueException
         * @throws \Exception
         */
        public function setIdentifierType($identifierType)
        {
            if (!Reflector::isValid(OrderReadRequest::class, $identifierType))
            {
                throw new InvalidPropertyValueException('identifierType');
            }
            $this->_identifierType = $identifierType;

            return $this;
        }

        /**
         * @return string
         */
        public function getIdentifier()
        {
            return $this->_identifier;
        }

        /**
         * @param string $identifier
         *
         * @return OrderReadRequest
         */
        public function setIdentifier($identifier)
        {
            $this->_identifier = $identifier;

            return $this;
        }
    }