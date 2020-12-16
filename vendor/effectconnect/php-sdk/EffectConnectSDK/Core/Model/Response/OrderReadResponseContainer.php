<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class OrderReadResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderReadResponseContainer implements ResponseContainerInterface
    {
        /**
         * @var Order $_order
         */
        private $_order;

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
            $this->_order = new Order($payload);
        }

        /**
         * @return Order
         */
        public function getOrder()
        {
            return $this->_order;
        }
    }