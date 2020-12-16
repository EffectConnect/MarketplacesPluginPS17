<?php
    namespace EffectConnect\PHPSdk\Core\Validation;

    use EffectConnect\PHPSdk\Core\Abstracts\Validator;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPayloadException;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\CallValidatorInterface;
    use EffectConnect\PHPSdk\Core\Model\Request\Order;
    use EffectConnect\PHPSdk\Core\Model\Request\OrderReadRequest;
    use EffectConnect\PHPSdk\Core\Model\Request\OrderUpdateRequest;

    /**
     * Class OrderValidator
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class OrderValidator extends Validator implements CallValidatorInterface
    {
        protected $validActions = [
            CallTypeInterface::ACTION_CREATE,
            CallTypeInterface::ACTION_READ,
            CallTypeInterface::ACTION_UPDATE
        ];
        /**
         * @param $argument
         *
         * @return bool
         * @throws InvalidPayloadException
         */
        public function validateCall($argument)
        {
            $valid = false;
            switch ($this->action)
            {
                case CallTypeInterface::ACTION_CREATE:
                    if ($argument instanceof Order)
                    {
                        $valid = true;
                    }
                    break;
                case CallTypeInterface::ACTION_READ:
                    if (
                        $argument instanceof OrderReadRequest &&
                        $argument->getIdentifierType() !== null &&
                        $argument->getIdentifier() !== null
                    )
                    {
                        $valid = true;
                    }
                    break;
                case CallTypeInterface::ACTION_UPDATE:
                    if (
                        $argument instanceof OrderUpdateRequest &&
                        count($argument->getOrderlines())+count($argument->getOrders()) > 0
                    )
                    {
                        $valid = true;
                    }
                    break;
            }
            if (!$valid)
            {
                throw new InvalidPayloadException($this->action);
            }

            return true;
        }
    }