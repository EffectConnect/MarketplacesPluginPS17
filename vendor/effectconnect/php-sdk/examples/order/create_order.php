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
     * 3. Create an EffectConnect\PHPSdk\Core\Model\Request\Order object and populate it with all required information
     */
    $orderNumber     = 'test_order';
    $currency        = 'EUR';
    $date            = new \DateTime('now', new \DateTimeZone('Europe/Amsterdam'));
    $shippingCost    = 0;
    $transactionCost = 0;
    try
    {
        $order = (new \EffectConnect\PHPSdk\Core\Model\Request\Order())
            ->setNumber($orderNumber)
            ->setCurrency($currency)
            ->setDate($date)
            ->addFee((new \EffectConnect\PHPSdk\Core\Model\Request\OrderFee())
                ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderFee::FEE_TYPE_SHIPPING)
                ->setAmount($shippingCost)
            )
            ->addFee((new \EffectConnect\PHPSdk\Core\Model\Request\OrderFee())
                ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderFee::FEE_TYPE_TRANSACTION)
                ->setAmount($transactionCost)
            )
        ;
        $shippingAddress = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderAddress())
            ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderAddress::TYPE_SHIPPING)
            ->setSalutation(\EffectConnect\PHPSdk\Core\Model\Request\OrderAddress::SALUTATION_MALE)
            ->setFirstName('Stefan')
            ->setLastName('Van den Heuvel')
            ->setCompany('Koek & Peer')
            ->setStreet('Tolhuisweg')
            ->setHouseNumber('5')
            ->setHouseNumberExtension('a')
            ->setAddressNote('Kantoor')
            ->setZipCode('6071RG')
            ->setCity('Swalmen')
            ->setState('Limburg')
            ->setCountry('NL')
            ->setPhone('0123456789')
            ->setEmail('stefan@koekenpeer.nl')
        ;
        $billingAddress = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderAddress())
            ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderAddress::TYPE_BILLING)
            ->setSalutation(\EffectConnect\PHPSdk\Core\Model\Request\OrderAddress::SALUTATION_MALE)
            ->setFirstName('Stefan')
            ->setLastName('Van den Heuvel')
            ->setCompany('EffectConnect')
            ->setStreet('SomeOtherStreet')
            ->setHouseNumber('12')
            ->setAddressNote('Home?')
            ->setZipCode('1234AB')
            ->setCity('ABCCity')
            ->setState('Somewhere')
            ->setCountry('NL')
            ->setPhone('9876543210')
            ->setEmail('stefan+1@koekenpeer.nl')
        ;

        $firstOrderLine = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderLine())
            ->setId('test_order_1')
            ->addProductIdentifier((new \EffectConnect\PHPSdk\Core\Model\Request\OrderLineProductIdentifier())
                ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderLineProductIdentifier::TYPE_IDENTIFIER)
                ->setValue(7)
            )
            ->setProductTitle('Optie 4')
            ->setQuantity(1)
            ->setIndividualProductPrice(12.50)
            ->addFee((new \EffectConnect\PHPSdk\Core\Model\Request\OrderFee())
                ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderFee::FEE_TYPE_COMMISSION)
                ->setAmount(1.25)
            )
        ;
        $secondOrderLine = (new \EffectConnect\PHPSdk\Core\Model\Request\OrderLine())
            ->setId('test_order_2')
            ->addProductIdentifier((new \EffectConnect\PHPSdk\Core\Model\Request\OrderLineProductIdentifier())
                ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderLineProductIdentifier::TYPE_SKU)
                ->setValue('OPTION-2')
            )
            ->setProductTitle('Optie 2')
            ->setQuantity(2)
            ->setIndividualProductPrice(12.50)
            ->addFee((new \EffectConnect\PHPSdk\Core\Model\Request\OrderFee())
                ->setType(\EffectConnect\PHPSdk\Core\Model\Request\OrderFee::FEE_TYPE_COMMISSION)
                ->setAmount(2.50)
            )
        ;
        $order
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress)
            ->addLine($firstOrderLine)
            ->addLine($secondOrderLine)
        ;
    } catch (\EffectConnect\PHPSdk\Core\Exception\InvalidSalutationException $invalidSalutationException)
    {
        echo $invalidSalutationException->getMessage();
        die();
    } catch (Exception $exception)
    {
        echo sprintf('Could not create Order object. `%s`', $exception->getMessage());
        die();
    }
    /**
     * 4. Make the call
     */
    $apiCall = $orderCallType->create($order);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');