<?php
    // 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');

    /**
     * @var \EffectConnect\PHPSdk\Core $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\OrderCall $orderCallType
     *
     * 2. Get the Order call type.
     */
    try
    {
        $orderCallType = $effectConnectSDK->OrderCall();
    } catch (Exception $exception)
    {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 3. Create an EffectConnect\PHPSdk\Core\Model\Request\Order object and populate it with the order number
     */
    try
    {
        $order = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderReadRequest())
            ->setIdentifierType(\EffectConnect\PHPSdk\Core\Model\Request\OrderReadRequest::TYPE_EFFECTCONNECT_NUMBER)
            ->setIdentifier('YOUR-IDENTIFIER')
        ;
    } catch (\EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException $invalidPropertyValueException)
    {
        echo $invalidPropertyValueException->getMessage();
        die();
    } catch (Exception $exception)
    {
        echo sprintf('Could not read Order object. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 4. Make the call
     */
    $apiCall = $orderCallType->read($order);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');