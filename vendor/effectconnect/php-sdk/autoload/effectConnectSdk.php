<?php
    /**
     * Autoloader in case Composer can't be used
     */
    spl_autoload_register(function ($class) {
        $sdkRoot       = realpath(__DIR__.'/..').'/';
        $namespaceFile = str_replace('\\', '/', $class).'.php';

        $namespaceFile = str_replace('EffectConnect/PHPSdk/', 'EffectConnectSDK/', $namespaceFile);
        $fileLocation  = $sdkRoot.$namespaceFile;
        if (file_exists($fileLocation))
        {
            require_once($fileLocation);
            return true;
        }
        throw new Exception(vsprintf('File `%s` for class `%s` not found', [$fileLocation, $class]));
    }, true);