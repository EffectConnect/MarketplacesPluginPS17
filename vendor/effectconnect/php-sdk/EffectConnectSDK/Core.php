<?php
    namespace EffectConnect\PHPSdk;

    use EffectConnect\PHPSdk\Core\CallType\ChannelListCall;
    use EffectConnect\PHPSdk\Core\CallType\ExternalStockListCall;
    use EffectConnect\PHPSdk\Core\CallType\LogCall;
    use EffectConnect\PHPSdk\Core\CallType\OrderCall;
    use EffectConnect\PHPSdk\Core\CallType\OrderListCall;
    use EffectConnect\PHPSdk\Core\CallType\ProcessCall;
    use EffectConnect\PHPSdk\Core\CallType\ProductsCall;
    use EffectConnect\PHPSdk\Core\CallType\ReportCall;
    use EffectConnect\PHPSdk\Core\Exception\InvalidApiCallException;
    use EffectConnect\PHPSdk\Core\Helper\Keychain;
    use EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface;

    /**
     * Class Core
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     *
     * @method OrderCall        OrderCall()
     * @method OrderListCall    OrderListCall()
     * @method ExternalStockListCall ExternalStockListCall()
     * @method ChannelListCall  ChannelListCall()
     * @method LogCall          LogCall()
     * @method ProductsCall     ProductsCall()
     * @method ProcessCall      ProcessCall()
     * @method ReportCall       ReportCall()
     */
    final class Core
    {
        /**
         * @var Keychain
         */
        private $_keychain;

        /**
         * @var ApiCall $_callClass
         */
        private $_callClass;

        /**
         * Core constructor.
         *
         * @param Keychain $keychain
         * @param ApiCall  $callClass
         *
         * @throws \Exception
         */
        public function __construct(Keychain $keychain, ApiCall $callClass=null)
        {
            if (!$keychain->_isValid())
            {
                throw new \Exception('Invalid keychain.');
            }
            if ($callClass === null)
            {
                $callClass = new ApiCall();
            }
            $this->_keychain  = $keychain;
            $this->_callClass = $callClass;
        }

        /**
         * @param $name
         * @param $arguments
         *
         * @return CallTypeInterface
         * @throws \Exception
         */
        final public function __call($name, $arguments)
        {
            try
            {
                $reflection = new \ReflectionClass('EffectConnect\PHPSdk\Core\CallType\\'.$name);
                /** @var CallTypeInterface $callType */
                $callType   = $reflection->newInstanceArgs([$this->_keychain, $this->_callClass]);

                return $callType;
            } catch (\Exception $exception)
            {
                throw new InvalidApiCallException($name);
            }
        }
    }
