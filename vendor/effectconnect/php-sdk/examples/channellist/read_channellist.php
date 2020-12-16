<?php
// 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');
    /**
     * @var \EffectConnect\PHPSdk\Core                        $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\ChannelListCall $channelListCallType
     *
     * 2. Get the OrderList call type.
     */
    try
    {
        $channelListCallType = $effectConnectSDK->ChannelListCall();
    } catch (Exception $exception) {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }

    /**
     * 4. Make the call
     */
    $apiCall = $channelListCallType->read();
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');
