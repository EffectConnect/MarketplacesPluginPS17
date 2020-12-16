<?php
// 1. Require the SDK base file.
    require_once(realpath(__DIR__ . '/..') . '/base.php');
    /**
     * @var \EffectConnect\PHPSdk\Core                  $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\LogCall $logCall
     *
     * 2. Get the Log call type.
     */
    $logCall = $effectConnectSDK->LogCall();
    /**
     * 3. Create a CURLFile containing the log entries
     */
    try
    {
        $logCreateFile = realpath(__DIR__) . '/files/log_create.xml';
        $curlFile      = new CURLFile($logCreateFile);
    } catch (Exception $exception)
    {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }

    /**
     * 4. Make the call
     */
    $apiCall = $logCall->create($curlFile);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__ . '/..') . '/result.php');
