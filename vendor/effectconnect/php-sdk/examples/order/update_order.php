<?php
    // 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');

    /**
     * @var EffectConnect\PHPSdk\Core\CallType\OrderCall $orderCallType
     *
     * 2. Get the Order call type.
     */
    try
    {
        $orderCallType = $effectConnectSDK->OrderCall();
        $orderCallType
            ->setResponseType(EffectConnect\PHPSdk\Core\Interfaces\CallTypeInterface::RESPONSE_TYPE_XML)
            ->setResponseLanguage('en')
        ;
    } catch (Exception $exception)
    {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 3. Create an EffectConnect\PHPSdk\Core\Model\Request\Order object containing all orderlines you're trying to update.
     */

    try
    {
        $orderAddTag             = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderUpdate())
            ->setOrderIdentifierType(\EffectConnect\PHPSdk\Core\Model\Request\OrderUpdate::TYPE_CHANNEL_NUMBER)
            ->setOrderIdentifier('TEST-ORDER-1')
            ->addTag('CustomTag')
            ->addTag('Test')
            ->removeTag('RemovableTag')
        ;
        $firstUpdatableOrderline = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderLineUpdate())
            ->setOrderlineIdentifierType(\EffectConnect\PHPSdk\Core\Model\Request\OrderLineUpdate::TYPE_EFFECTCONNECT_LINE_ID)
            ->setOrderlineIdentifier('test_order_1_1')
            ->setTrackingNumber('TEST-TRACK-1234')
            ->setTrackingUrl('https://test-update.test')
            ->setCarrier('NOT A CARRIER')
        ;
        $secondUpdatableOrderline = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderLineUpdate())
            ->setOrderlineIdentifierType(\EffectConnect\PHPSdk\Core\Model\Request\OrderLineUpdate::TYPE_EFFECTCONNECT_LINE_ID)
            ->setOrderlineIdentifier('test_order_1_2')
            ->setTrackingNumber('TEST-TRACK-1234')
            ->setTrackingUrl('https://test-update.test')
            ->setCarrier('NOT A CARRIER')
        ;
        $orderUpdate             = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderUpdateRequest())
            ->addLineUpdate($firstUpdatableOrderline)
            ->addLineUpdate($secondUpdatableOrderline)
            ->addOrderUpdate($orderAddTag)
        ;
    } catch (\EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException $invalidPropertyValueException)
    {
        echo $invalidPropertyValueException->getMessage();
        die();
    } catch (Exception $exception)
    {
        echo sprintf('Could not create object. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 4. Make the call
     */
    $apiCall = $orderCallType->update($orderUpdate);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');