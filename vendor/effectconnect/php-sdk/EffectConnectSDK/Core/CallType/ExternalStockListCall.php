<?php
    namespace EffectConnect\PHPSdk\Core\CallType;

    use EffectConnect\PHPSdk\Core\Abstracts\CallType;
    use EffectConnect\PHPSdk\ApiCall;
    use EffectConnect\PHPSdk\Core\Exception\InvalidActionForCallTypeException;
    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
    use EffectConnect\PHPSdk\Core\Model\Request\ExternalStockList;
    use EffectConnect\PHPSdk\Core\Model\Response\ExternalStockListReadResponseContainer;
    use EffectConnect\PHPSdk\Core\Validation\ExternalStockListValidator;

    /**
     * Class ExternalStockListCall
     *
     * CallType class receiving external stock from the EffectConnect Api.
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     * @method ApiCall read(ExternalStockList $list)
     */
    final class ExternalStockListCall extends CallType implements CallTypeInterface
    {
        protected $callVersion    = '2.0';
        protected $validatorClass = ExternalStockListValidator::class;

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
                ->setUri('/external_stocklist')
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
            return new ExternalStockListReadResponseContainer(Payload::extract($responsePayload, 'ExternalStockListReadResponseContainer'));
        }
    }