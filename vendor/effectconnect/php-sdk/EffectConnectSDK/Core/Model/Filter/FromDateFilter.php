<?php

    namespace EffectConnect\PHPSdk\Core\Model\Filter;

    use EffectConnect\PHPSdk\Core\Abstracts\ApiModel;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException;
    use EffectConnect\PHPSdk\Core\Interfaces\OrderListFilterInterface;

    /**
     * Class FromDateFilter
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class FromDateFilter extends ApiModel implements OrderListFilterInterface
    {
        /**
         * @var \DateTime $_filterValue
         */
        protected $_filterValue;

        /**
         * @return string
         */
        public function getName()
        {
            return 'fromDateFilter';
        }

        /**
         * @return string
         */
        public function getFilterValue()
        {
            return $this->_filterValue->format('Y-m-d\TH:i:sP');
        }

        /**
         * @param $filterValue
         *
         * @return FromDateFilter
         * @throws InvalidPropertyValueException
         */
        public function setFilterValue($filterValue)
        {
            if (!$filterValue instanceof \DateTime)
            {
                throw new InvalidPropertyValueException('filterValue');
            }

            $this->_filterValue = $filterValue;

            return $this;
        }
    }