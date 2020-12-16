<?php
    namespace EffectConnect\PHPSdk\Core\Interfaces;

    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException;

    /**
     * Interface OrderListFilterInterface
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    interface OrderListFilterInterface extends ApiModelInterface
    {
        public function getFilterValue();

        /**
         * @param $filterValue
         *
         * @return mixed
         * @throws InvalidPropertyValueException
         */
        public function setFilterValue($filterValue);
    }