<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    /**
     * Class Tag
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class Tag
    {
        private $_tag;

        /**
         * Tag constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_tag = $payload;
        }

        /**
         * @return mixed
         */
        public function getTag()
        {
            return $this->_tag;
        }
    }