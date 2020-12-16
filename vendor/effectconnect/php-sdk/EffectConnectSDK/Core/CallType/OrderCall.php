<?php
    namespace EffectConnect\PHPSdk\Core\CallType;

    use EffectConnect\PHPSdk\Core\Abstracts\CallType;
    use EffectConnect\PHPSdk\ApiCall;
    use EffectConnect\PHPSdk\Core\Exception\InvalidActionForCallTypeException;
    use EffectConnect\PHPSdk\Core\Helper\Payload;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
    use EffectConnect\PHPSdk\Core\Model\Request\Order;
    use EffectConnect\PHPSdk\Core\Model\Request\OrderReadRequest;
    use EffectConnect\PHPSdk\Core\Model\Request\OrderUpdateRequest;
    use EffectConnect\PHPSdk\Core\Model\Response\OrderCreateResponseContainer;
    use EffectConnect\PHPSdk\Core\Model\Response\OrderReadResponseContainer;
    use EffectConnect\PHPSdk\Core\Model\Response\OrderUpdateResponseContainer;
    use EffectConnect\PHPSdk\Core\Validation\OrderValidator;

    /**
     * Class OrderCall
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     * @method ApiCall create(Order $order)
     * @method ApiCall read(OrderReadRequest $readRequest)
     * @method ApiCall update(OrderUpdateRequest $updateRequest)
     */
    final class OrderCall extends CallType implements CallTypeInterface
    {
        protected $callVersion    = '2.0';
        protected $validatorClass = OrderValidator::class;

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
                case CallTypeInterface::ACTION_UPDATE:
                    $method = 'PUT';
                    break;
                default:
                    throw new InvalidActionForCallTypeException();
            }
            $apiCall
                ->setUri('/orders')
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
                case 'GET':
                    return new OrderReadResponseContainer(Payload::extract($responsePayload, 'OrderReadResponseContainer'));
                case 'POST':
                    return new OrderCreateResponseContainer(Payload::extract($responsePayload, 'OrderCreateResponseContainer'));
                case 'PUT':
                    return new OrderUpdateResponseContainer(Payload::extract($responsePayload, 'OrderUpdateResponseContainer'));
            }

            return null;
        }
    }
