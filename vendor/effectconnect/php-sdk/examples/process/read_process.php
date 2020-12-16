<?php
    // 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');
    /**
     * @var \EffectConnect\PHPSdk\Core                        $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\ProcessCall   $processCallType
     *
     * 2. Get the Process call type.
     */
    try
    {
        $processCallType = $effectConnectSDK->ProcessCall();
    } catch (Exception $exception)
    {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 3. Create an EffectConnect\PHPSdk\Core\Model\Request\ProcessReadRequest object and populate it with the process ID you want to retrieve
     */

    try
    {
        $processReadRequest = (new \EffectConnect\PHPSdk\Core\Model\Request\ProcessReadRequest())
            ->setID('abcdefghijklmnop')
        ;
    } catch (Exception $exception)
    {
        echo sprintf('Could not create object. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 4. Make the call
     */
    $apiCall = $processCallType->read($processReadRequest);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');