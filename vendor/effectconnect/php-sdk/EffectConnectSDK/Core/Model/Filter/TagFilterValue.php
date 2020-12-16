<?php
    namespace EffectConnect\PHPSdk\Core\Model\Filter;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;

    /**
     * Class TagFilterValue
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class TagFilterValue extends ApiModel implements ApiModelInterface
    {
        /**
         * @var string $_tagName
         */
        protected $_tagName;
        /**
         * @var bool $_exclude
         */
        protected $_exclude = false;

        /**
         * @return string
         */
        public function getName()
        {
            return 'filterValue';
        }

        /**
         * @return string
         */
        public function getTagName()
        {
            return $this->_tagName;
        }

        /**
         * @param string $tagName
         *
         * @return TagFilterValue
         */
        public function setTagName($tagName)
        {
            $this->_tagName = $tagName;

            return $this;
        }

        /**
         * @return bool
         */
        public function getExclude()
        {
            return $this->_exclude;
        }

        /**
         * @param bool $exclude
         *
         * @return TagFilterValue
         */
        public function setExclude($exclude)
        {
            $this->_exclude = $exclude;

            return $this;
        }
    }