<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class LogReadResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class LogReadResponseContainer implements ResponseContainerInterface
    {
        /**
         * @var bool $_reportInfo
         */
        private $_permitted;

        /**
         * ReportReadResponseContainer constructor.
         *
         * @param $payload
         */
        public function __construct($payload)
        {
            if ($payload === null)
            {
                return;
            }
            $this->_permitted = (int) Payload::extract($payload, 'permitted') === 1;
        }

        /**
         * @return bool
         */
        public function isPermitted()
        {
            return $this->_permitted;
        }
    }
