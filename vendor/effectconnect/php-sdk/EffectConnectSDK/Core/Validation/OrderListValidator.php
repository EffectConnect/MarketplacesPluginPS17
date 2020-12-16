<?php
    namespace EffectConnect\PHPSdk\Core\Validation;

    use EffectConnect\PHPSdk\Core\Abstracts\Validator;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPayloadException;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\CallValidatorInterface;
    use EffectConnect\PHPSdk\Core\Model\Request\OrderList;

    /**
     * Class OrderListValidator
     *
     * @author  Mark Thiesen
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class OrderListValidator extends Validator implements CallValidatorInterface
    {
        protected $validActions = [
            CallTypeInterface::ACTION_READ,
        ];

        /**
         * @param $argument
         *
         * @return bool
         * @throws InvalidPayloadException
         */
        public function validateCall($argument)
        {
            if (!$argument instanceof OrderList)
            {
                throw new InvalidPayloadException($this->action);
            }

            return true;
        }
    }