<?php
    namespace EffectConnect\PHPSdk\Core\Abstracts;

    use EffectConnect\PHPSdk\Core\Exception\InvalidActionForCallTypeException;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;

    /**
     * Class AbstractValidator
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     */
    abstract class Validator
    {
        /**
         * @var string $action
         */
        protected $action;

        protected $validActions = [
            CallTypeInterface::ACTION_CREATE,
            CallTypeInterface::ACTION_READ,
            CallTypeInterface::ACTION_UPDATE,
            CallTypeInterface::ACTION_DELETE
        ];

        /**
         * @param $callAction
         *
         * @throws InvalidActionForCallTypeException
         */
        final public function setup($callAction)
        {
            $this->action = $callAction;
            if (!in_array($callAction, $this->validActions))
            {
                throw new InvalidActionForCallTypeException();
            }
        }
    }