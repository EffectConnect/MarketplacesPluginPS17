<?php
    /**
     * Optional: Include the custom autoloader in case Composer is not available.
     */
    # require_once(realpath(__DIR__.'/..').'/autoload/effectConnectSdk.php');

    // 1. Set your public and secret API keys.
    $publicKey = 'PutYourSuppliedPublicKey';
    $secretKey = 'FillInYourOwnSecretKeyAsSupplied';
    // 2. Create a Keychain object.
    $keychain  = new EffectConnect\PHPSdk\Core\Helper\Keychain();
    try
    {
        // 3. Add your keys to the keychain.
        $keychain
            ->setPublicKey($publicKey)
            ->setSecretKey($secretKey)
        ;
    } catch (\Exception $exception)
    {
        echo sprintf('Could not set API keys. `%s`.', $exception->getMessage());
        die();
    }
    // 4. Instantiate the SDK
    try
    {
        $effectConnectSDK   = new EffectConnect\PHPSdk\Core($keychain);
    } catch (Exception $exception)
    {
        echo sprintf('Could not create SDK. `%s`', $exception->getMessage());
        die();
    }
    // All set! We can now make calls to the EffectConnect API!