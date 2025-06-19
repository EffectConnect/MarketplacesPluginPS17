<?php

namespace EffectConnect\Marketplaces\Service\Api;

use CURLFile;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Exception\ApiCallFailedException;
use EffectConnect\Marketplaces\Exception\CatalogExportFailedException;
use EffectConnect\Marketplaces\Exception\SdkCoreNotInitializedException;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Transformer\CatalogExportTransformer;
use Exception;

/**
 * Class CatalogExportApi
 * @package EffectConnect\Marketplaces\Service\Api
 */
class CatalogExportApi extends AbstractApi
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_CATALOG;

    /**
     * @var CatalogExportTransformer
     */
    protected $_catalogExportTransformer;

    /**
     * CatalogExportApi constructor.
     * @param LoggerHelper $loggerHelper
     * @param CatalogExportTransformer $catalogExportTransformer
     */
    public function __construct(
        LoggerHelper $loggerHelper,
        CatalogExportTransformer $catalogExportTransformer
    ) {
        $this->_catalogExportTransformer = $catalogExportTransformer;
        $this->_logger                   = $loggerHelper::createLogger(static::LOGGER_PROCESS);
    }

    /**
     * @param Connection $connection
     * @throws CatalogExportFailedException
     */
    public function exportCatalog(Connection $connection)
    {
        $this->logStart($connection);

        if (false === boolval($connection->catalog_export_active))
        {
            $this->_logger->warning('Catalog export skipped because process is disabled for current connection.', [
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
            $this->_logger->warning('Catalog export skipped because connection is not active.', [
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
            $this->_logger->error('Catalog export failed when initializing SDK.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new CatalogExportFailedException($connection->id, 'Initialize Sdk By Connection - ' . $e->getMessage());
        }

        try {
            $file = $this->_catalogExportTransformer->buildCatalogXml($connection);
        } catch (Exception $e) {
            $this->_logger->error('Catalog export failed when building catalog XML.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new CatalogExportFailedException($connection->id, 'Build Catalog Xml - ' . $e->getMessage());
        }

        if ($file !== false)
        {
            try {
                $curlFile = new CURLFile($file);
            } catch (Exception $e) {
                $this->_logger->error('Catalog export failed when initialize CURL file.', [
                    'process'    => static::LOGGER_PROCESS,
                    'message'    => $e->getMessage(),
                    'connection' => [
                        'id' => $connection->id
                    ]
                ]);
                $this->logEnd($connection);
                throw new CatalogExportFailedException($connection->id, 'Initialize CURL File - ' . $e->getMessage());
            }

            try {
                $this->productCreateCall($curlFile);
            } catch (Exception $e) {
                $this->_logger->error('Catalog export failed when doing create call to EffectConnect.', [
                    'process'    => static::LOGGER_PROCESS,
                    'message'    => $e->getMessage(),
                    'connection' => [
                        'id' => $connection->id
                    ]
                ]);
                $this->logEnd($connection);
                throw new CatalogExportFailedException($connection->id, 'Product Create Call - ' . $e->getMessage());
            }
        }

        $this->logEnd($connection);
    }

    /**
     * @param CURLFile $curlFile
     * @throws SdkCoreNotInitializedException
     * @throws ApiCallFailedException
     */
    protected function productCreateCall(CURLFile $curlFile)
    {
        $productsCall = $this->getSdkCore()->ProductsCall();
        $apiCall      = $productsCall->create($curlFile);
        $this->callAndResolveResponse($apiCall);
    }
}