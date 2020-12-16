<?php
// 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');
    /**
     * @var \EffectConnect\PHPSdk\Core                        $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\LogCall       $logCall
     *
     * 2. Get the Log call type.
     */
    $logCall = $effectConnectSDK->LogCall();

    /**
     * 3. Make the call
     */
    $apiCall = $logCall->read();
    $apiCall->call();
    /**
     * 4. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');
