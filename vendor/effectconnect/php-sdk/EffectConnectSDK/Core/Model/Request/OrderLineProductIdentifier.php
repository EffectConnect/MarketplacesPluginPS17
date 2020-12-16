<?php
    namespace EffectConnect\PHPSdk\Core\Model\Request;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class OrderLineProductIdentifier
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class OrderLineProductIdentifier extends ApiModel implements ApiModelInterface
    {
        const TYPE_ID           = 'ID';
        const TYPE_IDENTIFIER   = 'IDENTIFIER';
        const TYPE_EAN          = 'EAN';
        const TYPE_SKU          = 'SKU';
        /**
         * REQUIRED
         * @var string $_type
         */
        protected $_type;
        /**
         * REQUIRED
         * @var string|int $_value
         */
        protected $_value;
        
        public function getName()
        {
            return 'identifier';
        }

        /**
         * @return string
         */
        public function getType()
        {
            return $this->_type;
        }

        /**
         * @param string $type
         *
         * @return OrderLineProductIdentifier
         */
        public function setType($type)
        {
            $this->_type = $type;

            return $this;
        }

        /**
         * @return int|string
         */
        public function getValue()
        {
            return $this->_value;
        }

        /**
         * @param int|string $value
         *
         * @return OrderLineProductIdentifier
         */
        public function setValue($value)
        {
            $this->_value = $value;

            return $this;
        }
    }