<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class Order
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class Order extends ApiModel implements ApiModelInterface
    {
        /**
         * REQUIRED
         * @var string $_number
         *
         * Order number
         */
        protected $_number;

        /**
         * REQUIRED
         * @var string(3) $_currency
         *
         * Currency string in ISO 4217 format
         * http://www.currency-iso.org/dam/downloads/lists/list_one.xml
         */
        protected $_currency;

        /**
         * REQUIRED
         * @var \DateTime $_date
         *
         * The order date
         */
        protected $_date;
        /**
         * OPTIONAL
         * @var OrderFee[] $_fees
         *
         * A list of order fees.
         */
        protected $_fees = [];

        /**
         * REQUIRED
         * @var OrderAddress $_billingAddress
         *
         * Customer billing address
         */
        protected $_billingAddress;

        /**
         * REQUIRED
         * @var OrderAddress $_shippingAddress
         *
         * Customer shipping address
         */
        protected $_shippingAddress;

        /**
         * REQUIRED
         * @var OrderLine[] $_lines
         *
         * Order lines
         */
        protected $_lines = [];

        /**
         * @return string
         */
        public function getName()
        {
            return 'order';
        }

        /**
         * @return string
         */
        public function getNumber()
        {
            return $this->_number;
        }

        /**
         * @param string $number
         *
         * @return Order
         */
        public function setNumber($number)
        {
            $this->_number = $number;

            return $this;
        }

        /**
         * @return string
         */
        public function getDate()
        {
            if ($this->_date)
            {
                return $this->_date->format('Y-m-d\TH:i:sP');
            }

            return null;
        }

        /**
         * @param \DateTime $date
         *
         * @return Order
         */
        public function setDate(\DateTime $date)
        {
            $this->_date = $date;

            return $this;
        }

        /**
         * @return OrderAddress
         */
        public function getShippingAddress()
        {
            return $this->_shippingAddress;
        }

        /**
         * @return OrderAddress
         */
        public function getBillingAddress()
        {
            return $this->_billingAddress;
        }

        /**
         * @param OrderAddress $address
         *
         * @return Order
         */
        public function setShippingAddress(OrderAddress $address)
        {
            $this->_shippingAddress = $address->setType('shipping');

            return $this;
        }

        /**
         * @param OrderAddress $address
         *
         * @return Order
         */
        public function setBillingAddress(OrderAddress $address)
        {
            $this->_billingAddress = $address->setType('billing');

            return $this;
        }

        /**
         * @param OrderLine $orderLine
         *
         * @return Order
         */
        public function addLine($orderLine)
        {
            $this->_lines[] = $orderLine;

            return $this;
        }

        /**
         * @return OrderLine[]
         */
        public function getLines()
        {
            return $this->_lines;
        }

        /**
         * @return string
         */
        public function getCurrency()
        {
            return $this->_currency;
        }

        /**
         * @param string $currency
         *
         * @return Order
         */
        public function setCurrency($currency)
        {
            $this->_currency = $currency;

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
         * @return Order
         */
        public function addFee(OrderFee $fee)
        {
            $this->_fees[] = $fee;

            return $this;
        }
    }