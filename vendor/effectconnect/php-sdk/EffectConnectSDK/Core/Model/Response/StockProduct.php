<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class StockProduct
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class StockProduct
    {
        const FULFILMENT_STATUS_INTERNAL = 'internal';
        const FULFILMENT_STATUS_EXTERNAL = 'external';

        /**
         * @var int $_optionId
         */
        private $_optionId;

        /**
         * @var int $_stock
         */
        private $_stock;

        /**
         * @var int $_unsaleableStock
         */
        private $_unsaleableStock;

        /**
         * @var string $_fulfilmentStatus
         */
        private $_fulfilmentStatus;

        /**
         * @var \DateTime $_latestUpdate
         */
        private $_latestUpdate;

        /**
         * @var string $_sku
         */
        private $_sku;

        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }

            $this->_optionId         = Payload::extract($payload, 'optionId');
            $this->_stock            = Payload::extract($payload, 'stock');
            $this->_unsaleableStock  = Payload::extract($payload, 'unsaleableStock');
            $this->_fulfilmentStatus = Payload::extract($payload, 'fulfilmentStatus');
            $this->_latestUpdate     = \DateTime::createFromFormat('Y-m-d\TH:i:sP', Payload::extract($payload, 'latestUpdate'));
            $this->_sku              = Payload::extract($payload, 'sku');
        }

        /**
         * @return int
         */
        public function getOptionId()
        {
            return $this->_optionId;
        }

        /**
         * @return int
         */
        public function getStock()
        {
            return $this->_stock;
        }

        /**
         * @return int
         */
        public function getUnsaleableStock()
        {
            return $this->_unsaleableStock;
        }

        /**
         * @return string
         */
        public function getFulfilmentStatus()
        {
            return $this->_fulfilmentStatus;
        }

        /**
         * @return \DateTime
         */
        public function getLatestUpdate()
        {
            return $this->_latestUpdate;
        }

        /**
         * @return string|null
         */
        public function getSku()
        {
            return $this->_sku;
        }
    }
