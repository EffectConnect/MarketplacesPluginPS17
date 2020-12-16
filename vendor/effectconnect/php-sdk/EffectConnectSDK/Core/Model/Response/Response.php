<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;

    /**
     * Class Response
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class Response
    {
        const STATUS_SUCCESS        = 'Success';
        const STATUS_WARNING        = 'Warning';
        const STATUS_FAILURE        = 'Failure';
        /**
         * @var string $_result
         */
        private $_result;
        /**
         * @var ResponseContainerInterface $_responseContainer
         */
        private $_responseContainer;

        /**
         * @return string
         */
        final public function getResult()
        {
            return $this->_result;
        }

        /**
         * @param string $result
         *
         * @return Response
         */
        final public function setResult($result)
        {
            $this->_result = $result;

            return $this;
        }

        /**
         * @return ResponseContainerInterface
         */
        public function getResponseContainer()
        {
            return $this->_responseContainer;
        }

        /**
         * @param ResponseContainerInterface $responseContainer
         *
         * @return Response
         */
        public function setResponseContainer(ResponseContainerInterface $responseContainer)
        {
            $this->_responseContainer = $responseContainer;

            return $this;
        }
    }