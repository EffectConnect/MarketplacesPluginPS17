<?php
    namespace EffectConnect\PHPSdk\Core\CallType;

    use EffectConnect\PHPSdk\Core\Abstracts\CallType;
    use EffectConnect\PHPSdk\ApiCall;
    use EffectConnect\PHPSdk\Core\Exception\InvalidActionForCallTypeException;
    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
    use EffectConnect\PHPSdk\Core\Model\Response\LogCreateResponseContainer;
    use EffectConnect\PHPSdk\Core\Model\Response\LogReadResponseContainer;
    use EffectConnect\PHPSdk\Core\Validation\LogValidator;

    /**
     * Class LogCall
     *
     * CallType class for retrieving the process status report
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     * @method ApiCall read()
     * @method ApiCall create(\CURLFile $logFile)
     */
    final class LogCall extends CallType implements CallTypeInterface
    {
        protected $validatorClass = LogValidator::class;

        protected $callVersion = '2.0';

        /**
         * @param ApiCall $apiCall
         *
         * @return ApiCall
         * @throws InvalidActionForCallTypeException
         */
        public function _prepareCall($apiCall)
        {
            switch ($this->action)
            {
                case CallTypeInterface::ACTION_READ:
                    $method = 'GET';
                    break;
                case CallTypeInterface::ACTION_CREATE:
                    $method = 'POST';
                    break;
                default:
                    throw new InvalidActionForCallTypeException();
            }
            $apiCall
                ->setUri('/log')
                ->setMethod($method)
            ;

            return $apiCall;
        }

        /**
         * @param $method
         * @param $responsePayload
         *
         * @return ResponseContainerInterface
         */
        public static function processResponse($method, $responsePayload)
        {
            switch ($method)
            {
                case 'POST':
                    return new LogCreateResponseContainer(Payload::extract($responsePayload, 'LogCreateResponseContainer'));
                case 'GET':
                    return new LogReadResponseContainer(Payload::extract($responsePayload, 'LogReadResponseContainer'));
            }
            return null;
        }
    }
