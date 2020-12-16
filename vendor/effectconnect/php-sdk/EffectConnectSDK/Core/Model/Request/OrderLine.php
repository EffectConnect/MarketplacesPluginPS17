<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class OrderLine
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class OrderLine extends ApiModel implements ApiModelInterface
    {
        /**
         * REQUIRED
         * @var string $_id
         * 
         * The order line identifier
         */
        protected $_id;

        /**
         * REQUIRED
         * @var OrderLineProductIdentifier[] $_productIdentifiers
         * 
         * At least one identifier is required to match the product.
         */
        protected $_productIdentifiers = [];

        /**
         * REQUIRED
         * @var string $_productTitle
         */
        protected $_productTitle;

        /**
         * REQUIRED
         * @var int $_quantity
         * 
         * Defaults to 1
         */
        protected $_quantity = 1;

        /**
         * REQUIRED
         * @var float $_individualProductPrice
         * 
         * The price of a single product in this line
         */
        protected $_individualProductPrice;

        /**
         * OPTIONAL
         * @var OrderFee[] $_fees
         */
        protected $_fees = [];

        /**
         * @return string
         */
        public function getName()
        {
            return 'line';
        }

        /**
         * @return string
         */
        public function getId()
        {
            return $this->_id;
        }

        /**
         * @param string $id
         *
         * @return OrderLine
         */
        public function setId($id)
        {
            $this->_id = $id;

            return $this;
        }

        /**
         * @return OrderLineProductIdentifier[]
         */
        public function getProductIdentifiers()
        {
            return $this->_productIdentifiers;
        }

        /**
         * @param OrderLineProductIdentifier $identifier
         *
         * @return OrderLine
         */
        public function addProductIdentifier(OrderLineProductIdentifier $identifier)
        {
            $this->_productIdentifiers[] = $identifier;

            return $this;
        }

        /**
         * @return string
         */
        public function getProductTitle()
        {
            return $this->_productTitle;
        }

        /**
         * @param string $productTitle
         *
         * @return OrderLine
         */
        public function setProductTitle($productTitle)
        {
            $this->_productTitle = $productTitle;

            return $this;
        }

        /**
         * @return int
         */
        public function getQuantity()
        {
            return $this->_quantity;
        }

        /**
         * @param int $quantity
         *
         * @return OrderLine
         */
        public function setQuantity($quantity)
        {
            $this->_quantity = $quantity;

            return $this;
        }

        /**
         * @return float
         */
        public function getIndividualProductPrice()
        {
            return $this->_individualProductPrice;
        }

        /**
         * @param float $individualProductPrice
         *
         * @return OrderLine
         */
        public function setIndividualProductPrice($individualProductPrice)
        {
            $this->_individualProductPrice = $individualProductPrice;

            return $this;
        }

        /**
         * @return OrderFee[]
         */
        public function getFees()
        {
            return $this->_fees;
        }

        /**
         * @param OrderFee $fee
         *
         * @return OrderLine
         */
        public function addFee(OrderFee $fee)
        {
            $this->_fees[] = $fee;

            return $this;
        }
    }