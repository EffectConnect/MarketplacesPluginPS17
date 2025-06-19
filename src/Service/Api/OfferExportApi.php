<?php

namespace EffectConnect\Marketplaces\Service\Api;

use CURLFile;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Exception\ApiCallFailedException;
use EffectConnect\Marketplaces\Exception\OfferExportFailedException;
use EffectConnect\Marketplaces\Exception\SdkCoreNotInitializedException;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Transformer\OfferExportTransformer;
use Exception;

/**
 * Class OfferExportApi
 * @package EffectConnect\Marketplaces\Service\Api
 */
class OfferExportApi extends AbstractApi
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_OFFERS;

    /**
     * @var OfferExportTransformer
     */
    protected $_offerExportTransformer;

    /**
     * OfferExportApi constructor.
     * @param LoggerHelper $loggerHelper
     * @param OfferExportTransformer $offerExportTransformer
     */
    public function __construct(
        LoggerHelper $loggerHelper,
        OfferExportTransformer $offerExportTransformer
    ) {
        $this->_offerExportTransformer = $offerExportTransformer;
        $this->_logger                 = $loggerHelper::createLogger(static::LOGGER_PROCESS);
    }

    /**
     * @param Connection $connection
     * @param array $productIdsToExport
     * @throws OfferExportFailedException
     */
    public function exportOffers(Connection $connection, array $productIdsToExport = [])
    {
        $this->logStart($connection);

        if (false === boolval($connection->offer_export_active))
        {
            $this->_logger->warning('Offer export skipped because process is disabled for current connection.', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            return;
        }

        if (false === boolval($connection->is_active))
        {
            $this->_logger->warning('Offer export skipped because connection is not active.', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            return;
        }

        try {
            $this->initializeSdkByConnection($connection);
        } catch (Exception $e) {
            $this->_logger->error('Offer export failed when initializing SDK.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new OfferExportFailedException($connection->id, 'Initialize Sdk By Connection - ' . $e->getMessage());
        }

        try {
            $file = $this->_offerExportTransformer->buildOfferXml($connection, $productIdsToExport);
        } catch (Exception $e) {
            $this->_logger->error('Offer export failed when building catalog XML.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new OfferExportFailedException($connection->id, 'Build Offer Xml - ' . $e->getMessage());
        }

        if ($file !== false)
        {
            try {
                $curlFile = new CURLFile($file);
            } catch (Exception $e) {
                $this->_logger->error('Offer export failed when initialize CURL file.', [
                    'process'    => static::LOGGER_PROCESS,
                    'message'    => $e->getMessage(),
                    'connection' => [
                        'id' => $connection->id
                    ]
                ]);
                $this->logEnd($connection);
                throw new OfferExportFailedException($connection->id, 'Initialize CURL File - ' . $e->getMessage());
            }

            try {
                $this->productUpdateCall($curlFile);
            } catch (Exception $e) {
                $this->_logger->error('Offer export failed when doing update call to EffectConnect.', [
                    'process'    => static::LOGGER_PROCESS,
                    'message'    => $e->getMessage(),
                    'connection' => [
                        'id' => $connection->id
                    ]
                ]);
                $this->logEnd($connection);
                throw new OfferExportFailedException($connection->id, 'Product Create Call - ' . $e->getMessage());
            }
        }

        $this->logEnd($connection);
    }

    /**
     * @param CURLFile $curlFile
     * @throws ApiCallFailedException
     * @throws SdkCoreNotInitializedException
     */
    protected function productUpdateCall(CURLFile $curlFile)
    {
        $productsCall = $this->getSdkCore()->ProductsCall();
        $apiCall      = $productsCall->update($curlFile);
        $this->callAndResolveResponse($apiCall);
    }
}