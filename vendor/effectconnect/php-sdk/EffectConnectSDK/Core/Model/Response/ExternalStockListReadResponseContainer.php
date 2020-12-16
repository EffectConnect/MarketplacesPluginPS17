<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class ExternalStockListReadResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ExternalStockListReadResponseContainer implements ResponseContainerInterface
    {
        /**
         * @var int $_count
         */
        private $_count;
        /**
         * @var StockProduct[] $_stockProducts
         */
        private $_stockProducts = [];

        /**
         * ExternalStockListReadResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_count = (int)Payload::extract($payload, 'Count');
            foreach (Payload::extract($payload, 'StockProducts', true) as $stockProduct)
            {
                $this->_stockProducts[] = new StockProduct($stockProduct);
            }
        }

        /**
         * @return int
         */
        public function getCount()
        {
            return $this->_count;
        }

        /**
         * @return StockProduct[]
         */
        public function getStockProducts()
        {
            return $this->_stockProducts;
        }
    }
