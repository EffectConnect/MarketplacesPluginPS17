<?php
    namespace EffectConnect\PHPSdk\Core\Abstracts;

    use EffectConnect\PHPSdk\ApiCall;
    use EffectConnect\PHPSdk\Core\Exception\IncorrectArgumentException;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPayloadException;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyException;
    use EffectConnect\PHPSdk\Core\Exception\InvalidReflectionException;
    use EffectConnect\PHPSdk\Core\Exception\InvalidResponseTypeException;
    use EffectConnect\PHPSdk\Core\Exception\InvalidValidatorClassException;
    use EffectConnect\PHPSdk\Core\Exception\MissingValidatorClassException;
    use EffectConnect\PHPSdk\Core\Helper\Keychain;
    use EffectConnect\PHPSdk\Core\Helper\Reflector;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\CallValidatorInterface;

    /**
     * Class AbstractCall
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    abstract class CallType
    {
        /**
         * @var string $_action
         */
        protected $action;

        /**
         * @var ApiCall $callClass
         *
         * The CURL base class
         */
        protected $callClass;

        /**
         * @var \DateTime $callDate
         *
         * Time the call is made.
         */
        protected $callDate;

        /**
         * @var string $callVersion
         */
        protected $callVersion = '1.0';

        /**
         * @var Keychain $keychain
         *
         * Key container for basic key validation
         */
        protected $keychain;

        /**
         * @var mixed $payload
         */
        protected $payload;

        /**
         * @var string $responseLanguage
         *
         * Defines the response language (feedback and errors)
         */
        protected $responseLanguage = 'en';

        /**
         * @var string $responseType
         *
         * Defines how the response will be formatted
         */
        protected $responseType = CallTypeInterface::RESPONSE_TYPE_XML;

        /**
         * @var CallValidatorInterface $validator
         */
        protected $validator;

        /**
         * @var string $validatorClass
         */
        protected $validatorClass;

        /**
         * AbstractCall constructor.
         *
         * @param Keychain $keychain
         * @param ApiCall  $callClass
         *
         * @throws InvalidValidatorClassException
         * @throws MissingValidatorClassException
         */
        public function __construct(Keychain $keychain, ApiCall $callClass)
        {
            $this->callClass = $callClass;
            $this->keychain  = $keychain;
            $this->callDate  = new \DateTime('now', new \DateTimeZone('Europe/Amsterdam'));
            if (!$this->validatorClass)
            {
                throw new MissingValidatorClassException();
            }
            if (!class_exists($this->validatorClass))
            {
                throw new InvalidValidatorClassException();
            }
            $this->validator = new $this->validatorClass();
        }

        /**
         * @param $name
         * @param $arguments
         *
         * @return ApiCall
         * @throws IncorrectArgumentException
         * @throws InvalidPayloadException
         * @throws InvalidPropertyException
         */
        public function __call($name, $arguments)
        {
            $this->payload = array_shift($arguments);
            $this->action  = $name;
            $this->validator->setup($this->action);
            $this->validator->validateCall($this->payload);

            return $this->prepareCall();
        }

        /**
         * @param $responseLanguage
         *
         * @return $this
         */
        final public function setResponseLanguage($responseLanguage = 'en')
        {
            $this->responseLanguage = $responseLanguage;

            return $this;
        }

        /**
         * @param $responseType
         *
         * @return $this
         *
         * @throws InvalidResponseTypeException
         */
        final public function setResponseType($responseType = CallTypeInterface::RESPONSE_TYPE_XML)
        {
            try {
                if (Reflector::isValid(CallTypeInterface::class, $responseType, 'RESPONSE_TYPE_%') === true)
                {
                    $this->responseType = $responseType;
                } else
                {
                    throw new InvalidResponseTypeException();
                }
            } catch (InvalidReflectionException $e)
            {
                throw new InvalidResponseTypeException();
            } catch (\ReflectionException $e)
            {
                throw new InvalidResponseTypeException();
            }

            return $this;
        }

        /**
         * @return ApiCall
         * @throws InvalidPropertyException
         */
        final public function prepareCall()
        {
            $this->callClass
                ->setResponseType($this->responseType)
                ->setResponseLanguage($this->responseLanguage)
                ->setCallDate($this->callDate)
                ->setCallVersion($this->callVersion)
                ->setPublicKey($this->keychain->getPublicKey())
                ->setSecretKey($this->keychain->getSecretKey())
                ->setPayload($this->payload)
                ->setParseCallback(get_class($this).'::processResponse')
            ;

            return $this->_prepareCall($this->callClass);
        }

        /**
         * @param ApiCall $apiCall
         *
         * @return ApiCall
         */
        protected abstract function _prepareCall($apiCall);
    }
