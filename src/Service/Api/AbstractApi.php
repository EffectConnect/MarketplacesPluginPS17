<?php

namespace EffectConnect\Marketplaces\Service\Api;

use EffectConnect\Marketplaces\Exception\ApiCallFailedException;
use EffectConnect\Marketplaces\Exception\InvalidApiCredentialsException;
use EffectConnect\Marketplaces\Exception\SdkCoreNotInitializedException;
use EffectConnect\Marketplaces\Exception\UnknownException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\PHPSdk\ApiCall;
use EffectConnect\PHPSdk\Core;
use EffectConnect\PHPSdk\Core\Exception\InvalidKeyException;
use EffectConnect\PHPSdk\Core\Helper\Keychain;
use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
use EffectConnect\PHPSdk\Core\Model\Response\Response;
use Exception;
use Monolog\Logger;

/**
 * Class AbstractApi
 * @package EffectConnect\Marketplaces\Service\Api
 */
class AbstractApi
{
    /**
     * @var bool
     */
    protected $_initialized = false;

    /**
     * @var Core
     */
    protected $_sdkCore;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var int
     */
    protected $_timeOut = 300;

    /**
     * @param Connection $connection
     * @throws InvalidApiCredentialsException
     * @throws UnknownException
     */
    public function initializeSdkByConnection(Connection $connection)
    {
        $this->initializeSdk($connection->public_key, $connection->secret_key);
    }

    /**
     * @param string $publicKey
     * @param string $secretKey
     * @throws InvalidApiCredentialsException
     * @throws UnknownException
     */
    public function initializeSdk(string $publicKey, string $secretKey)
    {
        $this->_initialized = false;

        try {
            $this->_sdkCore = new Core(
                (new Keychain())
                    ->setPublicKey($publicKey)
                    ->setSecretKey($secretKey)
            );
            $this->_initialized = true;
        } catch (InvalidKeyException $e) {
            throw new InvalidApiCredentialsException($publicKey, $secretKey);
        } catch (Exception $e) {
            throw new UnknownException($e->getMessage());
        }
    }

    /**
     * @return Core
     * @throws SdkCoreNotInitializedException
     */
    public function getSdkCore()
    {
        if (!$this->_initialized) {
            throw new SdkCoreNotInitializedException();
        }
        return $this->_sdkCore;
    }

    /**
     * Resolve the API call response.
     *
     * @param ApiCall $apiCall
     * @param int $timeOut
     * @return ResponseContainerInterface
     * @throws ApiCallFailedException
     */
    public function callAndResolveResponse(ApiCall $apiCall, int $timeOut = 0): ResponseContainerInterface
    {
        if ($timeOut > 0) {
            $this->_timeOut = $timeOut;
        }

        $apiCall->setTimeout($this->_timeOut)->call();
        if (!$apiCall->isSuccess())
        {
            $errorMessageString = '[' . implode('] [', $apiCall->getErrors()) . ']';
            throw new ApiCallFailedException($errorMessageString);
        }

        $response   = $apiCall->getResponseContainer();
        $result     = $response->getResponse()->getResult();

        // Check if response is successful
        if ($result == Response::STATUS_FAILURE)
        {
            $errorMessages = [];
            foreach ($response->getErrorContainer()->getErrorMessages() as $errorMessage)
            {
                $errorMessages[] = vsprintf('%s. Code: %s. Message: %s', [
                    $errorMessage->getSeverity(),
                    $errorMessage->getCode(),
                    $errorMessage->getMessage()
                ]);
            }
            $errorMessageString = '[' . implode('] [', $errorMessages) . ']';
            throw new ApiCallFailedException($errorMessageString);
        }

        return $response->getResponse()->getResponseContainer();
    }

    /**
     * @param Connection $connection
     */
    public function logStart(Connection $connection)
    {
        $this->_logger->info('Process started.', [
            'process'    => static::LOGGER_PROCESS,
            'connection' => [
                'id'                                        => $connection->id,
                'name'                                      => $connection->name,
                'public_key'                                => $connection->public_key,
                'is_active'                                 => $connection->is_active,
                'id_shop'                                   => $connection->id_shop,
                'catalog_export_only_active'                => $connection->catalog_export_only_active,
                'catalog_export_special_price'              => $connection->catalog_export_special_price,
                'catalog_export_ean_leading_zero'           => $connection->catalog_export_ean_leading_zero,
                'catalog_export_skip_invalid_ean'           => $connection->catalog_export_skip_invalid_ean,
                'catalog_export_skip_unavailable_for_order' => $connection->catalog_export_skip_unavailable_for_order,
                'order_import_id_carrier'                   => $connection->order_import_id_carrier,
                'order_import_id_payment_module'            => $connection->order_import_id_payment_module,
                'order_import_send_emails'                  => $connection->order_import_send_emails,
                'order_import_api_call_timeout'             => $connection->order_import_api_call_timeout,
            ]
        ]);
    }

    /**
     * @param Connection $connection
     */
    public function logEnd(Connection $connection)
    {
        $this->_logger->info('Process ended.', [
            'process'    => static::LOGGER_PROCESS,
            'connection' => [
                'id' => $connection->id
            ]
        ]);
    }
}