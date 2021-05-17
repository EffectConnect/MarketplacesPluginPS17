<?php

namespace EffectConnect\Marketplaces\Model;

use Db;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use PrestaShopException;

/**
 * Class TrackingExportQueue
 * @package EffectConnect\Marketplaces\Model
 */
class TrackingExportQueue extends AbstractModel
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_SHIPMENT;

    /**
     * @var int Prestashop order ID
     */
    public $id_order = 0;

    /**
     * @var int EffectConnect Connection ID
     */
    public $id_connection = 0;

    /**
     * @var string EffectConnect Marketplaces Order Number (used for order identification)
     */
    public $ec_marketplaces_identification_number = '';

    /**
     * @var string|null EffectConnect Marketplaces Channel Number
     */
    public $ec_marketplaces_channel_number = null;

    /**
     * @var string EffectConnect Marketplaces Line IDs (JSON data, used for order line identification)
     */
    public $ec_marketplaces_order_line_ids = '';

    /**
     * @var bool
     */
    public $is_shipped = false;

    /**
     * @var string|null
     */
    public $carrier_name = null;

    /**
     * @var string|null
     */
    public $tracking_number = null;

    /**
     * @var string|null
     */
    public $shipped_exported_at = null;

    /**
     * @var string|null
     */
    public $tracking_exported_at = null;

    /**
     * @var array
     */
    public static $definition = [
        'table'     => 'ec_tracking_export_queue',
        'primary'   => 'id_tracking_export_queue',
        'multilang' => false,
        'fields'    => [
            'id_order' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
            ],
            'id_connection' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
            ],
            'ec_marketplaces_identification_number' => [
                'type'       => self::TYPE_STRING,
                'required'   => true,
                'size'       => 64
            ],
            'ec_marketplaces_channel_number' => [
                'type'       => self::TYPE_STRING,
                'required'   => false,
                'size'       => 64,
                'allow_null' => true
            ],
            'ec_marketplaces_order_line_ids' => [
                'type'       => self::TYPE_STRING,
                'required'   => true,
                'size'       => 65535,
            ],
            'is_shipped' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'carrier_name' => [
                'type'       => self::TYPE_STRING,
                'required'   => false,
                'size'       => 64,
                'allow_null' => true
            ],
            'tracking_number' => [
                'type'       => self::TYPE_STRING,
                'required'   => false,
                'size'       => 64,
                'allow_null' => true
            ],
            'shipped_exported_at' => [
                'type'       => self::TYPE_DATE,
                'required'   => false,
                'allow_null' => true
            ],
            'tracking_exported_at' => [
                'type'       => self::TYPE_DATE,
                'required'   => false,
                'allow_null' => true
            ],
        ]
    ];

    /**
     * @param array $orderLineIds
     * @return $this
     */
    public function setFormattedEcMarketplacesOrderLineIds(array $orderLineIds)
    {
        $this->ec_marketplaces_order_line_ids = json_encode($orderLineIds);
        return $this;
    }

    /**
     * @return array
     */
    public function getFormattedEcMarketplacesOrderLineIds()
    {
        $formatted = json_decode($this->ec_marketplaces_order_line_ids);
        if (is_array($formatted)) {
            return $formatted;
        }
        return [];
    }

    //
    // TODO:
    //   The functions below use SQL!
    //   Can we use Symfony for this?
    //

    /**
     * @return bool
     */
    public static function createDbTable()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`(
                    `id_tracking_export_queue` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_order` INT UNSIGNED NOT NULL,
                    `id_connection` INT UNSIGNED NOT NULL,
                    `ec_marketplaces_identification_number` VARCHAR(64) NOT NULL,
                    `ec_marketplaces_channel_number` VARCHAR(64) NULL DEFAULT NULL,
                    `ec_marketplaces_order_line_ids` TEXT NOT NULL,
                    `is_shipped` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',
                    `carrier_name` VARCHAR(64) NULL DEFAULT NULL,
                    `tracking_number` VARCHAR(64) NULL DEFAULT NULL,
                    `shipped_exported_at` DATETIME NULL DEFAULT NULL,
                    `tracking_exported_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id_tracking_export_queue`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')
            ;
    }

    /**
     * @return bool
     */
    public static function removeDbTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`');
    }

    /**
     * @param int $idConnection
     * @return TrackingExportQueue[]
     */
    public static function getListToExport(int $idConnection)
    {
        $where = '`id_connection` = ' . intval($idConnection) . '
            AND (
                (`shipped_exported_at` IS NULL AND `is_shipped` = 1)
                OR
                (`tracking_exported_at` IS NULL AND `tracking_number` IS NOT NULL)
            )
        ';
        return static::getList($where, 30);
    }

    /**
     * @param int $idOrder
     * @return TrackingExportQueue[]
     */
    public static function getListByIdOrder(int $idOrder)
    {
        $where = '`id_order` = ' . intval($idOrder);
        return static::getList($where);
    }

    /**
     * @param string $effectConnectNumber
     * @return TrackingExportQueue[]
     */
    public static function getListByEffectConnectNumber(string $effectConnectNumber)
    {
        $where = '`ec_marketplaces_identification_number` = \'' . pSQL($effectConnectNumber) . '\'';
        return static::getList($where);
    }

    /**
     * @param int $idOrder
     * @param int $isShipped
     * @return bool
     */
    public static function updateIsShipped(int $idOrder, int $isShipped)
    {
        $result = false;
        foreach (self::getListByIdOrder($idOrder) as $trackingExportQueue)
        {
            // Only update status if it is not exported yet to EC - updates are not sent to EffectConnect.
            if ($trackingExportQueue->shipped_exported_at === null)
            {
                $trackingExportQueue->is_shipped = $isShipped;
                try {
                    $trackingExportQueue->save(true);
                } catch (PrestaShopException $e) {
                    continue;
                }
                $result = true;
            }
        }

        $logger = LoggerHelper::createLogger(static::LOGGER_PROCESS);
        $logger->info('Hook updateIsShipped was called.', [
            'process'    => static::LOGGER_PROCESS,
            'id_order'   => $idOrder,
            'is_shipped' => $isShipped,
            'result'     => intval($result)
        ]);

        return $result;
    }

    /**
     * Add tracking number to the order export queue database for current Prestashop order ID.
     * @param int $idOrder
     * @param string $carrierName
     * @param string $trackingNumber
     * @return bool
     */
    public static function updateCarrierNameAndTrackingNumber(int $idOrder, string $carrierName, string $trackingNumber)
    {
        $result = false;
        foreach (self::getListByIdOrder($idOrder) as $trackingExportQueue)
        {
            // Only insert tracking numbers that are not exported yet to EC - updates are not sent to EffectConnect.
            if ($trackingExportQueue->tracking_exported_at === null)
            {
                $trackingExportQueue->tracking_number = $trackingNumber;
                $trackingExportQueue->carrier_name    = $carrierName;
                try {
                    $trackingExportQueue->save(true);
                } catch (PrestaShopException $e) {
                    continue;
                }
                $result = true;
            }
        }

        $logger = LoggerHelper::createLogger(static::LOGGER_PROCESS);
        $logger->info('Hook updateCarrierNameAndTrackingNumber was called.', [
            'process'        => static::LOGGER_PROCESS,
            'id_order'       => $idOrder,
            'carrierName'    => $carrierName,
            'trackingNumber' => $trackingNumber,
            'result'         => intval($result)
        ]);

        return $result;
    }
}