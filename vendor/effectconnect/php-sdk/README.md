# EffectConnectSDK
SDK for EffectConnect API integration

This is a simple SDK to start connecting to our API.

So far this SDK only includes functionality and examples to create an order and inject a productfeed.


More information about the EffectConnect API: [EffectConnect Docs](https://docs.effectconnect.com)
# Getting started

## Step 1: Installing the project 
Including the project can be done via composer:
```
    composer require effectconnect/php-sdk
```
Or by downloading the source code and including the autoloader:
```php
    require_once(realpath(__DIR__.'/..').'/autoload/effectConnectSdk.php');
```

## Step 2: Creating your API Keyset
For this step you'll need to go to your EffectConnect environment and create an API Keyset.

Make sure you assign all the required permissions to this keyset in order to use the calls you intend to.

## Step 3: Basics
To use any API call you are required to create a **Keychain** and instantiate a Core object.
```php
    use EffectConnect\PHPSdk\Core\Helper\Keychain;
    use EffectConnect\PHPSdk\Core;
    
    $keychain  = (new Keychain())
        ->setPublicKey('YourPublicKey')
        ->setSecretKey('YourSecretKey')
    ;
    // Instantiate the API Core
    $core = new Core($keychain);
```

## Step 4: Creating an Api Call object
At the moment, the following Call objects are available:
* OrderCall
* OrderListCall
* ProductsCall
* ProcessCall
* ReportCall

These objects can be obtained by calling their respective methods in the Core:
```php
    $orderCall = $core->OrderCall();
```

## Step 5: Preparing your Api Call
All Api calls have their own requirements and will be validated. If invalid information is being passed to the call, an `InvalidPayloadException` will be thrown.

In this example we will read an order with identifier `EC-ORDER-ID`;
```php
    use EffectConnect\PHPSdk\Core\Model\Request\OrderReadRequest;
    $orderReadRequest = (new OrderReadRequest())
        ->setIdentifierType(OrderReadRequest::TYPE_EFFECTCONNECT_NUMBER)
        ->setIdentifier('EC-ORDER-ID')
    ;
    $apiCall = $orderCall->read($orderReadRequest);
```

Now that we have our prepared `ApiCall` object, we can make the call to EffectConnect. At this stage no communication has taken place, the `ApiCall::call()` method is the first (and only) time you will connect with the EffectConnect API server.
```php
    $apiCall->call();
```

## Step 6: Checking the call
Now that we've made a call to EffectConnect, it's time to check whether everything has gone the way we intended to or if everything went the way of the dodo...

In case something went terribly wrong, the `ApiCall::isSuccess()` method will return false and more information about this failure should be found via the `ApiCall::getErrors()` method.
```php
    if (!$apiCall->isSuccess()) 
    {
        // Check $apiCall->getErrors() for errors that have occurred.
    }
``` 

## Step 7: Process your response
Yay, everything went **exactly** as we planned! Now it's time to do something with the response. The SDK contains objects for all the Response Containers.

This means you can access and process any information EffectConnect returns to you. In our example, an instance of `OrderReadResponseContainer` will be contained in our `Response` object.

Let's find out which identifiers are assigned to our `EC-ORDER-ID`!
```php
    // The EffectConnect\PHPSdk\Core\Model\Response\ApiResponseContainer
    $responseContainer = $apiCall->getResponseContainer();
    // The EffectConnect\PHPSdk\Core\Model\Response\Response
    $response          = $responseContainer->getResponse();
    if ($response->getResult() === Response::STATUS_SUCCESS)
    {
        // The EffectConnect\PHPSdk\Core\Model\Response\OrderReadResponseContainer
        $orderResponse = $response->getResponseContainer();
        // Our EffectConnect\PHPSdk\Core\Model\Response\Order
        $order         = $orderResponse->getOrder();
        echo '<pre>';
        print_r($order->getIdentifiers());
        echo '</pre>';
    }
``` 