<?php

namespace EffectConnect\Marketplaces\Model;

use Db;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use PrestaShopException;

/**
 * Class OfferExportQueue
 * @package EffectConnect\Marketplaces\Model
 */
class OfferExportQueue extends AbstractModel
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_OFFERS;

    /**
     * @var int Prestashop product ID
     */
    public $id_product = 0;

    /**
     * @var string|null
     */
    public $date_add = null;

    /**
     * @var string|null
     */
    public $exported_at = null;

    /**
     * @var array
     */
    public static $definition = [
        'table'     => 'ec_stock_export_queue',
        'primary'   => 'id_stock_export_queue',
        'multilang' => false,
        'fields'    => [
            'id_product' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
            ],
            'date_add' => [
                'type'       => self::TYPE_DATE,
                'validate'   => 'isDate'
            ],
            'exported_at' => [
                'type'       => self::TYPE_DATE,
                'required'   => false,
                'allow_null' => true
            ],
        ]
    ];

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
                    `id_stock_export_queue` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_product` INT UNSIGNED NOT NULL,
                    `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `exported_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`id_stock_export_queue`)
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
     * @param int $idProduct
     */
    public static function schedule(int $idProduct)
    {
        // Don't schedule items that are already scheduled.
        $where = '`id_product` = ' . intval($idProduct) . ' AND `exported_at` IS NULL';
        $existingRecords = static::getList($where);
        if (count($existingRecords) > 0 ) {
            return;
        }

        // Schedule item.
        $record             = new OfferExportQueue();
        $record->id_product = $idProduct;
        try {
            $result = $record->save(true);
        } catch (PrestaShopException $e) {
            $result = false;
        }

        $logger = LoggerHelper::createLogger(static::LOGGER_PROCESS);
        $logger->info('Hook ecOfferExportQueueSchedule was called.', [
            'process'    => static::LOGGER_PROCESS,
            'id_product' => $idProduct,
            'result'     => intval($result)
        ]);
    }

    /**
     * @return OfferExportQueue[]
     */
    public static function getListToExport()
    {
        $where = '`exported_at` IS NULL';
        return static::getList($where);
    }
}