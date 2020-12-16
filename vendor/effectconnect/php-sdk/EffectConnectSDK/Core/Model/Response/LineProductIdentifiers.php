<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class LineProductIdentifiers
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class LineProductIdentifiers
    {
        /**
         * @var string $_id
         */
        private $_id;
        /**
         * @var string $_ean
         */
        private $_ean;
        /**
         * @var string $_sku
         */
        private $_sku;
        /**
         * @var string $_identifier
         */
        private $_identifier;

        /**
         * LineProductIdentifiers constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_id  = Payload::extract($payload, 'ID');
            $this->_ean = Payload::extract($payload, 'EAN');
            $this->_sku = Payload::extract($payload, 'SKU');
            $this->_identifier = Payload::extract($payload, 'identifier');
        }

        /**
         * @return string
         */
        public function getId()
        {
            return $this->_id;
        }

        /**
         * @return string
         */
        public function getEan()
        {
            return $this->_ean;
        }

        /**
         * @return string
         */
        public function getSku()
        {
            return $this->_sku;
        }

        /**
         * @return string
         */
        public function getIdentifier()
        {
            return $this->_identifier;
        }
    }
