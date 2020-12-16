<?php
// 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');
    /**
     * @var \EffectConnect\PHPSdk\Core                        $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\OrderListCall $orderListCallType
     *
     * 2. Get the OrderList call type.
     */
    try
    {
        $orderListCallType = $effectConnectSDK->OrderListCall();
    } catch (Exception $exception) {
        echo sprintf('Could not create call type. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 3. Create an EffectConnect\PHPSdk\Core\Model\Request\OrderList object and populate it with the type and values
     */
    $fromDateExampleFilter = new \EffectConnect\PHPSdk\Core\Model\Filter\FromDateFilter();
    $toDateExampleFilter   = new \EffectConnect\PHPSdk\Core\Model\Filter\ToDateFilter();
    $statusExampleFilter   = new \EffectConnect\PHPSdk\Core\Model\Filter\HasStatusFilter();
    $tagExampleFilter      = new \EffectConnect\PHPSdk\Core\Model\Filter\HasTagFilter();
    try
    {
        /**
         * Example: Retrieve all orders placed AFTER 01-04-2018 at 04:00:00
         */
        $fromDateExampleFilter->setFilterValue(new DateTime('2018-04-01 04:00:00', new DateTimeZone('Europe/Amsterdam')));
    } catch (\EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException $invalidPropertyValueException)
    {
        echo $invalidPropertyValueException->getMessage();
        die();
    }
    try
    {
        /**
         * Example: Retrieve all orders placed BEFORE today
         */
        $toDateExampleFilter->setFilterValue(new DateTime('now', new DateTimeZone('Europe/Amsterdam')));
    } catch (\EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException $invalidPropertyValueException)
    {
        echo $invalidPropertyValueException->getMessage();
        die();
    }
    try
    {
        /**
         * Example: Retrieve all orders having either "Paid" or "Cancelled" status.
         */
        $statusExampleFilter->setFilterValue([
            \EffectConnect\PHPSdk\Core\Model\Filter\HasStatusFilter::STATUS_PAID,
            \EffectConnect\PHPSdk\Core\Model\Filter\HasStatusFilter::STATUS_CANCELLED
        ]);
    } catch (\EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException $invalidPropertyValueException)
    {
        echo $invalidPropertyValueException->getMessage();
        die();
    }
    try
    {
        /**
         * Example: Retrieve all orders containing the "Test" tag.
         */
        $tagExampleFilter->setFilterValue([
            (new \EffectConnect\PHPSdk\Core\Model\Filter\TagFilterValue())
                ->setTagName('Test')
        ]);
    } catch (\EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException $invalidPropertyValueException)
    {
        echo $invalidPropertyValueException->getMessage();
        die();
    }
    try
    {
        $orderList = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderList())
            ->addFilter($fromDateExampleFilter)
            ->addFilter($toDateExampleFilter)
            ->addFilter($statusExampleFilter)
            ->addFilter($tagExampleFilter)
        ;
    } catch (\EffectConnect\PHPSdk\Core\Exception\MissingFilterValueException $missingFilterValueException)
    {
        echo $missingFilterValueException->getMessage();
        die();
    }
    /**
     * 4. Make the call
     */
    $apiCall = $orderListCallType->read($orderList);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');