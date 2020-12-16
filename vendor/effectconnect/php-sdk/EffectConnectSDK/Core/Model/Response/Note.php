<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    /**
     * Class Note
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class Note
    {
        /**
         * @var string $_note
         */
        private $_note;

        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_note = $payload;
        }

        /**
         * @return string
         */
        public function getNote()
        {
            return $this->_note;
        }
    }