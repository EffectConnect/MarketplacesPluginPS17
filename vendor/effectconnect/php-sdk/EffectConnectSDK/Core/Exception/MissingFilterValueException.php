<?php

    namespace EffectConnect\PHPSdk\Core\Exception;

    /**
     * Class MissingFilterValueException
     *
     * @author  Stefan Van den Heuvel
     * @company Koek & Peer
     * @product EffectConnect
     * @package EffectConnectSDK
     */
    final class MissingFilterValueException extends \Exception
    {
        public function __construct($filterClass)
        {
            parent::__construct(vsprintf('Missing value for filter `%s`.', [$filterClass]));
        }
    }