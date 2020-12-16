<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class OrderUpdateRequest
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderUpdateRequest extends ApiModel implements ApiModelInterface
    {
        /**
         * @var OrderLineUpdate[] $_orderlines
         */
        protected $_orderlines  = [];
        /**
         * @var OrderUpdate[] $_orders
         */
        protected $_orders      = [];

        /**
         * @return string
         */
        public function getName()
        {
            return 'update';
        }

        /**
         * @return OrderUpdate[]
         */
        public function getOrders()
        {
            return $this->_orders;
        }

        /**
         * @return OrderLineUpdate[]
         */
        public function getOrderlines()
        {
            return $this->_orderlines;
        }

        /**
         * @param OrderUpdate $orderUpdate
         *
         * @return OrderUpdateRequest
         */
        public function addOrderUpdate(OrderUpdate $orderUpdate)
        {
            $this->_orders[] = $orderUpdate;

            return $this;
        }

        /**
         * @param OrderLineUpdate $line
         *
         * @return OrderUpdateRequest
         */
        public function addLineUpdate(OrderLineUpdate $lineUpdate)
        {
            $this->_orderlines[] = $lineUpdate;

            return $this;
        }
    }