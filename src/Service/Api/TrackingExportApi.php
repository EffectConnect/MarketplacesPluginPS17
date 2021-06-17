<?php

namespace EffectConnect\Marketplaces\Service\Api;

use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Exception\ApiCallFailedException;
use EffectConnect\Marketplaces\Exception\SdkCoreNotInitializedException;
use EffectConnect\Marketplaces\Exception\TrackingExportFailedException;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Model\TrackingExportQueue;
use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException;
use EffectConnect\PHPSdk\Core\Model\Request\OrderLineUpdate;
use EffectConnect\PHPSdk\Core\Model\Request\OrderUpdate;
use EffectConnect\PHPSdk\Core\Model\Request\OrderUpdateRequest;
use Exception;
use PrestaShopException;

/**
 * Class TrackingExportApi
 * @package EffectConnect\Marketplaces\Service\Api
 */
class TrackingExportApi extends AbstractApi
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_SHIPMENT;

    /**
     * OrderImportApi constructor.
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(
        LoggerHelper $loggerHelper
    ) {
        $this->_logger = $loggerHelper::createLogger(static::LOGGER_PROCESS);
    }

    /**
     * @param Connection $connection
     * @throws TrackingExportFailedException
     */
    public function exportTrackingNumbers(Connection $connection)
    {
        $this->logStart($connection);

        if (false === boolval($connection->is_active))
        {
            $this->_logger->warning('Tracking export skipped because connection is not active.', [
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
            $this->_logger->error('Tracking export failed when initializing SDK.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new TrackingExportFailedException($connection->id, 'Tracking export failed when initializing SDK - ' . $e->getMessage()); // TODO: logn
        }

        $trackingNumbersToExport = TrackingExportQueue::getListToExport($connection->id);
        $this->_logger->info(count($trackingNumbersToExport) . ' tracking number(s) to export.', [
            'process'    => static::LOGGER_PROCESS,
            'connection' => [
                'id' => $connection->id
            ]
        ]);
        foreach ($trackingNumbersToExport as $trackingNumberToExport)
        {
            // Save that we are exporting this tracking code to prevent other cronjobs to process the same item.
            // Bad luck if the export fails, we will not try to do this again. All tracking items we export (either
            // by order state 'shipped' or when a tracking number was added) will get the 'shipped' status in
            // EffectConnect. Adding a tracking number (and carrier) to the order update is optional.
            // Each type of export is done once (set EC order to 'shipped' and add tracking number - we won't do
            // any updates).
            $trackingNumberToExport->shipped_exported_at = date('Y-m-d H:i:s', time());
            $trackingNumberToExport->is_shipped          = 1;
            if ($trackingNumberToExport->carrier_name !== null || $trackingNumberToExport->tracking_number !== null) {
                $trackingNumberToExport->tracking_exported_at = date('Y-m-d H:i:s', time());
            }
            try {
                $trackingNumberToExport->save();
            } catch (PrestaShopException $e) {
                $this->_logger->error('Failed to mark tracking number as exported. Skipped current tracking export.', [
                    'process'         => static::LOGGER_PROCESS,
                    'message'         => $e->getMessage(),
                    'connection'      => [
                        'id' => $connection->id
                    ],
                    'tracking_export' => [
                        'id' => $trackingNumberToExport->id
                    ]
                ]);
                continue;
            }

            // Update EC order update with shop order number and ID.
            try {
                $this->trackingExportCall(
                    $trackingNumberToExport->ec_marketplaces_identification_number,
                    $trackingNumberToExport->getFormattedEcMarketplacesOrderLineIds(),
                    $trackingNumberToExport->carrier_name,
                    $trackingNumberToExport->tracking_number
                );
            } catch (Exception $e) {
                $this->_logger->error('Failed to mark tracking number as exported. Skipped current tracking export.', [
                    'process'         => static::LOGGER_PROCESS,
                    'message'         => $e->getMessage(),
                    'connection'      => [
                        'id' => $connection->id
                    ],
                    'tracking_export' => [
                        'id'                                    => $trackingNumberToExport->id,
                        'ec_marketplaces_identification_number' => $trackingNumberToExport->ec_marketplaces_identification_number,
                        'ec_marketplaces_channel_number'        => $trackingNumberToExport->ec_marketplaces_channel_number,
                        'ec_marketplaces_order_line_ids'        => $trackingNumberToExport->ec_marketplaces_order_line_ids,
                        'carrier_name'                          => $trackingNumberToExport->carrier_name,
                        'tracking_number'                       => $trackingNumberToExport->tracking_number
                    ]
                ]);
            }
        }

        $this->logEnd($connection);
    }

    /**
     * @param string $effectConnectNumber
     * @param array $effectConnectLineIds
     * @param string|null $carrier
     * @param string|null $trackingNumber
     * @throws ApiCallFailedException
     * @throws InvalidPropertyValueException
     * @throws SdkCoreNotInitializedException
     */
    protected function trackingExportCall(string $effectConnectNumber, array $effectConnectLineIds, string $carrier = null, string $trackingNumber = null)
    {
        $orderCall = $this->getSdkCore()->OrderCall();

        $orderData = new OrderUpdate();
        $orderData
            ->setOrderIdentifierType(OrderUpdate::TYPE_EFFECTCONNECT_NUMBER)
            ->setOrderIdentifier($effectConnectNumber);

        $orderUpdate = new OrderUpdateRequest();
        $orderUpdate->addOrderUpdate($orderData);

        foreach ($effectConnectLineIds as $effectConnectLineId)
        {
            $orderLineUpdate = (new OrderLineUpdate())
                ->setOrderlineIdentifierType(OrderLineUpdate::TYPE_EFFECTCONNECT_ID)
                ->setOrderlineIdentifier($effectConnectLineId);

            if ($carrier !== null) {
                $orderLineUpdate->setCarrier($carrier);
            }

            if ($trackingNumber !== null) {
                $orderLineUpdate->setTrackingNumber($trackingNumber);
            }

            $orderUpdate->addLineUpdate($orderLineUpdate);
        }

        $apiCall = $orderCall->update($orderUpdate);
        $this->callAndResolveResponse($apiCall);
    }
}