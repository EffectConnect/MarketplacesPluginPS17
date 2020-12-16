<?php

    // 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');
    /**
     * @var \EffectConnect\PHPSdk\Core                                $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\ExternalStockListCall $externalStockListType
     *
     * 2. Get the ExternalStockListCall call type.
     */
    try
    {
        $externalStockListType = $effectConnectSDK->ExternalStockListCall();
    } catch (Exception $exception) {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 3. Create an EffectConnect\PHPSdk\Core\Model\Request\ExternalStockList object and populate it with the type and values
     */
        $orderList = (new \EffectConnect\PHPSdk\Core\Model\Request\ExternalStockList())
            ->setChannelId(1234);
    /**
     * 4. Make the call
     */
    $apiCall = $externalStockListType->read($orderList);
    $apiCall->setTimeout(30);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');
