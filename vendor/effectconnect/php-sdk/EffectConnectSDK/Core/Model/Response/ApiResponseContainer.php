<?php
    namespace EffectConnect\PHPSdk\Core\Model\Response;

    /**
     * Class ApiResponseContainer
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class ApiResponseContainer
    {
        /**
         * @var Request $_request
         */
        private $_request;
        /**
         * @var Response $_response
         */
        private $_response;
        /**
         * @var ErrorContainer $_errorContainer
         */
        private $_errorContainer;

        /**
         * @return Request
         */
        public function getRequest()
        {
            return $this->_request;
        }

        /**
         * @param Request $request
         *
         * @return ApiResponseContainer
         */
        public function setRequest($request)
        {
            $this->_request = $request;

            return $this;
        }

        /**
         * @return Response
         */
        public function getResponse()
        {
            return $this->_response;
        }

        /**
         * @param Response $response
         *
         * @return ApiResponseContainer
         */
        public function setResponse(Response $response)
        {
            $this->_response = $response;

            return $this;
        }

        /**
         * @return ErrorContainer
         */
        public function getErrorContainer()
        {
            return $this->_errorContainer;
        }

        /**
         * @param ErrorContainer $errorContainer
         *
         * @return ApiResponseContainer
         */
        public function setErrorContainer($errorContainer)
        {
            $this->_errorContainer = $errorContainer;

            return $this;
        }
    }