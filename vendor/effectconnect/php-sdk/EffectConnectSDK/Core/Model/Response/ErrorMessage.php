<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;

    /**
     * Class ErrorMessage
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ErrorMessage
    {
        private $_severity;
        private $_code;
        private $_message;

        /**
         * ErrorMessage constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_severity = Payload::extract($payload, 'Severity');
            $this->_code     = Payload::extract($payload, 'Code');
            $this->_message  = Payload::extract($payload, 'Message');
        }

        /**
         * @return mixed
         */
        public function getSeverity()
        {
            return $this->_severity;
        }

        /**
         * @return mixed
         */
        public function getCode()
        {
            return $this->_code;
        }

        /**
         * @return mixed
         */
        public function getMessage()
        {
            return $this->_message;
        }
    }