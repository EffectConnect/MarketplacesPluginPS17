<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class Response
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    abstract class ProcessResponseContainer implements ResponseContainerInterface
    {
        /***
         * @var string $_processId
         */
        private $_processId;
        /**
         * OrderUpdateResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload===null)
            {
                return;
            }
            $this->_processId = Payload::extract($payload, 'ProcessID');
        }

        /**
         * @return string
         */
        final public function getProcessId()
        {
            return $this->_processId;
        }
    }