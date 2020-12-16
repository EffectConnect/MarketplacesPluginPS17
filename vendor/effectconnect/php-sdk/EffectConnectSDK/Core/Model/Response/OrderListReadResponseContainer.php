<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class OrderListReadResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderListReadResponseContainer implements ResponseContainerInterface
    {
        /**
         * @var int $_count
         */
        private $_count;
        /**
         * @var Order[] $_orders
         */
        private $_orders = [];

        /**
         * OrderListReadResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            if (($this->_count = (int)Payload::extract($payload, 'Count')) > 0)
            {
                foreach (Payload::extract($payload, 'Orders', true) as $order)
                {
                    $this->_orders[] = new Order($order);
                }
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
         * @return Order[]
         */
        public function getOrders()
        {
            return $this->_orders;
        }
    }
