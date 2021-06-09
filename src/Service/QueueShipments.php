<?php

namespace EffectConnect\Marketplaces\Service;

use Carrier;
use Configuration;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use EffectConnect\Marketplaces\Model\TrackingExportQueue;
use Monolog\Logger;
use Order;
use OrderCarrier;
use OrderState;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Class QueueShipments
 *
 * @package EffectConnect\Marketplaces\Service
 */
class QueueShipments
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_SHIPMENT;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * QueueShipments constructor.
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(
        LoggerHelper $loggerHelper
    ) {
        $this->_logger = $loggerHelper::createLogger(static::LOGGER_PROCESS);
    }

    /**
     * Queue tracking numbers and shipments for export to EffectConnect that are not queued by any hook due to external shipment plugins.
     */
    public function execute()
    {
        // Fetch unshipped EC orders
        $trackingExportQueueItems = TrackingExportQueue::getListNotShipped();

        $this->_logger->info('QueueShipments process started - ' . count($trackingExportQueueItems) . ' unshipped orders to check.', [
            'process' => static::LOGGER_PROCESS,
        ]);

        // Loop all imported EffectConnect orders that are not shipped yet
        foreach ($trackingExportQueueItems as $trackingExportQueueItem)
        {
            $idOrder = intval($trackingExportQueueItem->id_order);

            // Fetch PrestaShop order
            try {
                $order = new Order($idOrder);
            } catch (PrestaShopDatabaseException $e) {
                $this->_logger->error('Order when fetching order ' . $idOrder . '.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
                continue;
            } catch (PrestaShopException $e) {
                $this->_logger->error('Order when fetching order ' . $idOrder . '.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
                continue;
            }

            // Fetch PrestaShop order state
            try {
                $orderState = new OrderState($order->current_state);
            } catch (PrestaShopDatabaseException $e) {
                $this->_logger->error('Order when fetching order state ' . $order->current_state . '.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
                continue;
            } catch (PrestaShopException $e) {
                $this->_logger->error('Order when fetching order state ' . $order->current_state . '.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
                continue;
            }

            // Mark order as shipped in the export queue in case the order has been shipped
            if (boolval($orderState->shipped) === true) {
                TrackingExportQueue::updateIsShipped($idOrder, 1);
            } else {
                $this->_logger->info('Order ' . $idOrder . ' is not shipped yet.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
            }

            // Fetch PrestaShop order carrier
            try {
                $idOrderCarrier = intval($order->getIdOrderCarrier());
                $orderCarrier   = new OrderCarrier($idOrderCarrier);
                $idCarrier      = intval($orderCarrier->id_carrier);
                $carrier        = new Carrier($idCarrier);
            } catch (PrestaShopDatabaseException $e) {
                $this->_logger->error('Order when fetching carrier info for order ' . $idOrder . '.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
                continue;
            } catch (PrestaShopException $e) {
                $this->_logger->error('Order when fetching carrier info for order ' . $idOrder . '.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
                continue;
            }

            // Add tracking info to order in the export queue (only when we have a tracking number)
            $trackingNumber = strval($orderCarrier->tracking_number);
            $carrierName    = strval($carrier->name);
            if (!empty($trackingNumber)) {
                TrackingExportQueue::updateCarrierNameAndTrackingNumber($idOrder, $carrierName, $trackingNumber);
            } else {
                $this->_logger->info('Order ' . $idOrder . ' has no tracking number yet.', [
                    'process' => static::LOGGER_PROCESS,
                ]);
            }
        }
    }
}