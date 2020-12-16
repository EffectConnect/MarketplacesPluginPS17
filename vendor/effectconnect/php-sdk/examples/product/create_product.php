<?php
    // 1. Require the SDK base file.
    require_once(realpath(__DIR__.'/..').'/base.php');

    /**
     * @var \EffectConnect\PHPSdk\Core $effectConnectSDK
     * @var \EffectConnect\PHPSdk\Core\CallType\ProductsCall $productCallType
     *
     * 2. Get the Product call type.
     */
    $productsCallType = $effectConnectSDK->ProductsCall();
    /**
     * 3. Create a CURLFile containing the product feed
     */
    try
    {
        $productCreateFileLocation = realpath(__DIR__).'/files/product_create.xml';
        $curlFile                  = new CURLFile($productCreateFileLocation);
    } catch (Exception $exception)
    {
        echo sprintf('Error in creating CURLFile: `%s`', $exception->getMessage());
        die();
    }
    /**
     * 4. Make the call
     */
    $apiCall = $productsCallType->create($curlFile);
    $apiCall->call();
    /**
     * 5. Handle call result
     */
    require_once(realpath(__DIR__.'/..').'/result.php');