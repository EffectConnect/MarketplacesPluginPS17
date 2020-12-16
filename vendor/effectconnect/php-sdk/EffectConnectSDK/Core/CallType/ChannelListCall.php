<?php
    namespace EffectConnect\PHPSdk\Core\CallType;

    use EffectConnect\PHPSdk\Core\Abstracts\CallType;
    use EffectConnect\PHPSdk\ApiCall;
    use EffectConnect\PHPSdk\Core\Exception\InvalidActionForCallTypeException;
    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
    use EffectConnect\PHPSdk\Core\Model\Request\ChannelListRequest;
    use EffectConnect\PHPSdk\Core\Model\Response\ChannelListReadResponseContainer;
    use EffectConnect\PHPSdk\Core\Validation\ChannelListValidator;

    /**
     * Class ChannelListCall
     *
     * CallType class receiving orders from the EffectConnect Api.
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     * @method ApiCall read()
     */
    final class ChannelListCall extends CallType implements CallTypeInterface
    {
        protected $callVersion    = '2.0';
        protected $validatorClass = ChannelListValidator::class;

        public function __call($name, $arguments)
        {
            return parent::__call($name, [new ChannelListRequest()]);
        }

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
                default:
                    throw new InvalidActionForCallTypeException();
            }
            $apiCall
                ->setUri('/channellist')
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
            return new ChannelListReadResponseContainer(Payload::extract($responsePayload, 'ChannelListReadResponseContainer'));
        }
    }
