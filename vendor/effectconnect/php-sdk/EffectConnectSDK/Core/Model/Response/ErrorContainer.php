<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    /**
     * Class ErrorContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ErrorContainer
    {
        /**
         * @var ErrorMessage[] $_errorMessages
         */
        private $_errorMessages = [];

        /**
         * ErrorContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            foreach ($payload as $message)
            {
                $this->_errorMessages[] = new ErrorMessage($message);
            }
        }

        /**
         * @return ErrorMessage[]
         */
        public function getErrorMessages()
        {
            return $this->_errorMessages;
        }
    }