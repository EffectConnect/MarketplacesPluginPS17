<?php
    namespace EffectConnect\PHPSdk\Core\Validation;

    use EffectConnect\PHPSdk\Core\Abstracts\Validator;
    use EffectConnect\PHPSdk\Core\Exception\InvalidPayloadException;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;
    use EffectConnect\PHPSdk\Core\Interfaces\CallValidatorInterface;
    use EffectConnect\PHPSdk\Core\Model\Request\ReportReadRequest;

    /**
     * Class ReportValidator
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    final class ReportValidator extends Validator implements CallValidatorInterface
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
            $valid = false;
            switch ($this->action) {
                case CallTypeInterface::ACTION_READ:
                    if ($argument instanceof ReportReadRequest) {
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