<?php
    // 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');
    /**
     * @var \EffectConnect\PHPSdk\Core                       $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\ReportCall   $reportCallType
     *
     * 2. Get the Process call type.
     */
    try
    {
        $reportCallType = $effectConnectSDK->ReportCall();
    } catch (Exception $exception)
    {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 3. Create an EffectConnect\PHPSdk\Core\Model\Request\ReportReadRequest object and populate it with the process ID you want to retrieve
     */

    try
    {
        $reportReadRequest = (new \EffectConnect\PHPSdk\Core\Model\Request\ReportReadRequest())
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
    $apiCall = $reportCallType->read($reportReadRequest);
    $apiCall->call();

    require_once(realpath(__DIR__.'/..').'/result.php');