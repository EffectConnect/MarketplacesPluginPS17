<?php
    namespace EffectConnect\PHPSdk;

    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyException;
    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\ApiModelInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
    use EffectConnect\PHPSdk\Core\Model\Response\ApiResponseContainer;
    use EffectConnect\PHPSdk\Core\Model\Response\ErrorContainer;
    use EffectConnect\PHPSdk\Core\Model\Response\Request;
    use EffectConnect\PHPSdk\Core\Model\Response\Response;

    /**
     * Class ApiCall
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class ApiCall
    {
        const API_ENDPOINT = 'https://submit.effectconnect.com/v1';
        /**
         * @var \DateTime $_callDate
         */
        protected $_callDate;

        /**
         * @var string $_callVersion
         */
        protected $_callVersion = '1.0';

        /**
         * @var array $_curlInfo
         */
        protected $_curlInfo = [];

        /**
         * @var string $_curlResponse
         */
        protected $_curlResponse;

        /**
         * @var array $_errors
         */
        protected $_errors = [];

        /**
         * @var array $_headers
         */
        protected $_headers;

        /**
         * @var string $_method
         *
         */
        protected $_method;

        /**
         * @var mixed $_payload
         */
        protected $_payload;

        /**
         * @var string $_publicKey
         */
        protected $_publicKey;

        /**
         * @var string $_responseLanguage
         */
        protected $_responseLanguage = 'en';

        /**
         * @var string $_responseType
         */
        protected $_responseType = CallTypeInterface::RESPONSE_TYPE_XML;

        /**
         * @var string $_parseCallback
         */
        protected $_parseCallback;

        /**
         * @var string $_secretKey
         */
        protected $_secretKey;

        /**
         * @var int $_timeout
         */
        protected $_timeout = 3;

        /**
         * @var string $_uri
         *
         * The endpoint we're attempting to reach
         */
        protected $_uri;

        /**
         * @return ApiCall
         */
        public function call()
        {
            $postFields = $this->_payload;
            if ($this->_payload instanceof \CURLFile)
            {
                /**
                 * Allowing a longer timeout to upload the file to EffectConnect.
                 */
                $this->_timeout = 30;
                /**
                 * Sending the CURLFile as an array.
                 */
                $postFields     = [$this->_payload];
            }
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER      => $this->_getHeaders(),
                CURLOPT_URL             => self::API_ENDPOINT.$this->_uri,
                CURLOPT_CUSTOMREQUEST   => $this->_method,
                CURLOPT_TIMEOUT         => $this->_timeout,
                CURLOPT_POSTFIELDS      => $postFields,
                CURLOPT_RETURNTRANSFER  => true
            ]);
            $this->_curlResponse = curl_exec($ch);
            if (($curlError = curl_error($ch)) !== '')
            {
                $this->_errors[] = curl_error($ch);
            }
            if (($errNo = (int)curl_errno($ch)) > 0)
            {
                $this->_errors[] = sprintf('Curl error %d', $errNo);
            }
            $this->_curlInfo = curl_getinfo($ch);
            curl_close($ch);
            if ($this->_curlResponse === '')
            {
                $this->_errors[] = sprintf('No response received: `%s`',
                    ((int)$curlError === CURLE_OPERATION_TIMEDOUT?'Operation timed out. Extend your timeout (ApiCall::setTimeout()).':'Unknown reason')
                );
            }
            if ((int)$this->_curlInfo['http_code'] !== 200)
            {
                $this->_errors[] = sprintf('Invalid http code: `%d`', (int)$this->_curlInfo['http_code']);
            }

            return $this;
        }

        /**
         * @param string $parseCallback
         *
         * @return ApiCall
         */
        public function setParseCallback($parseCallback)
        {
            $this->_parseCallback = $parseCallback;

            return $this;
        }


        /**
         * @return array
         */
        public function getErrors()
        {
            return $this->_errors;
        }

        /**
         * @return string
         */
        public function getRawResponse()
        {
            return $this->_curlResponse;
        }

        /**
         * @return ApiResponseContainer
         */
        public function getResponseContainer()
        {
            $payload = json_decode($this->_curlResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE)
            {
                $payload = simplexml_load_string($this->_curlResponse);
            }
            $responsePayload   = Payload::extract($payload, 'Response');
            $response          = (new Response())->setResult(Payload::extract($responsePayload, 'Result'));
            $container         = (new ApiResponseContainer())->setRequest(new Request(Payload::extract($payload, 'Request')));
            if (($responseContainer = call_user_func_array($this->_parseCallback, [$this->_method, $responsePayload])) instanceof ResponseContainerInterface)
            {
                $response->setResponseContainer($responseContainer);
            }
            $container
                ->setResponse($response)
                ->setErrorContainer(new ErrorContainer(Payload::extract($payload, 'ErrorContainer', true)))
            ;

            return $container;
        }

        /**
         * @return bool
         */
        public function isSuccess()
        {
            return (count($this->_errors) === 0);
        }

        /**
         * @param \DateTime $callDate
         *
         * @return ApiCall
         */
        public function setCallDate(\DateTime $callDate)
        {
            $this->_callDate = $callDate;

            return $this;
        }

        /**
         * @param string $callVersion
         *
         * @return ApiCall
         */
        public function setCallVersion($callVersion)
        {
            $this->_callVersion = $callVersion;

            return $this;
        }

        /**
         * @param array $headers
         *
         * @return ApiCall
         */
        public function setHeaders(array $headers)
        {
            $this->_headers = $headers;

            return $this;
        }

        /**
         * @param string $method
         *
         * @return ApiCall
         */
        public function setMethod($method)
        {
            $this->_method = $method;

            return $this;
        }

        /**
         * @param ApiModelInterface|\CURLFile|null $payload
         *
         * @return ApiCall
         * @throws InvalidPropertyException
         */
        public function setPayload($payload=null)
        {
            if ($payload instanceof ApiModelInterface)
            {
                $this->_payload = $payload->getXml();
            } elseif ($payload instanceof \CURLFile)
            {
                $this->_payload = $payload;
            }

            return $this;
        }

        /**
         * @param string $publicKey
         *
         * @return ApiCall
         */
        public function setPublicKey($publicKey)
        {
            $this->_publicKey = $publicKey;

            return $this;
        }

        /**
         * @param string $responseLanguage
         *
         * @return ApiCall
         */
        public function setResponseLanguage($responseLanguage)
        {
            $this->_responseLanguage = $responseLanguage;

            return $this;
        }

        /**
         * @param string $responseType
         *
         * @return ApiCall
         */
        public function setResponseType($responseType)
        {
            $this->_responseType = $responseType;

            return $this;
        }

        /**
         * @param string $secretKey
         *
         * @return ApiCall
         */
        public function setSecretKey($secretKey)
        {
            $this->_secretKey = $secretKey;

            return $this;
        }

        /**
         * @param string $uri
         *
         * @return ApiCall
         */
        public function setUri($uri)
        {
            $this->_uri = $uri;

            return $this;
        }

        /**
         * @param $timeout
         *
         * @return ApiCall
         */
        public function setTimeout($timeout)
        {
            $this->_timeout = $timeout;

            return $this;
        }

        /**
         * @return array
         */
        protected function _getHeaders()
        {
            $headers = [
                'KEY: '.$this->_publicKey,
                'VERSION: '.$this->_callVersion,
                'URI: '.$this->_uri,
                'RESPONSETYPE: '.$this->_responseType,
                'RESPONSELANGUAGE: '.$this->_responseLanguage,
                'TIME: '.$this->_callDate->format('Y-m-d\TH:i:sP'),
                'SIGNATURE: '.$this->_signApiCall()
            ];
            if ($this->_payload instanceof \CURLFile)
            {
                /**
                 * Setting the Content-Type header for sending our file.
                 */
                $headers[] = 'Content-Type: text/xml';
            }

            return $headers;
        }

        /**
         * @return string
         */
        protected function _signApiCall()
        {
            if ($this->_payload instanceof \CURLFile)
            {
                $size = filesize($this->_payload->getFilename());
            } else
            {
                $size = strlen((string)$this->_payload);
            }
            $digest = [
                $size,
                $this->_method,
                $this->_uri,
                $this->_callVersion,
                $this->_callDate->format('Y-m-d\TH:i:sP')
            ];
            return base64_encode(hash_hmac('sha512', implode('', $digest), $this->_secretKey, true));
        }
    }
