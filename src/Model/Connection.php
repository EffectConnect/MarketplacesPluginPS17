<?php

namespace EffectConnect\Marketplaces\Model;

use Configuration;
use Db;
use EffectConnect\Marketplaces\Enums\ConfigurationKeys;
use EffectConnect\Marketplaces\Enums\ExternalFulfilment;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Class Connection
 * @package EffectConnect\Marketplaces\Model
 */
class Connection extends AbstractModel
{
    /**
     * @var bool
     */
    public $is_active = false;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int
     */
    public $id_shop = 0;

    /**
     * @var string
     */
    public $public_key = '';

    /**
     * @var string
     */
    public $secret_key = '';

    /**
     * @var bool
     */
    public $catalog_export_only_active = true;

    /**
     * @var bool
     */
    public $catalog_export_add_option_title = true;

    /**
     * @var bool
     */
    public $catalog_export_special_price = true;

    /**
     * @var bool
     */
    public $catalog_export_ean_leading_zero = true;

    /**
     * @var bool
     */
    public $catalog_export_skip_invalid_ean = false;

    /**
     * @var bool
     */
    public $catalog_export_skip_unavailable_for_order = true;

    /**
     * @var int
     */
    public $order_import_id_group = 0;

    /**
     * @var int
     */
    public $order_import_id_carrier = 0;

    /**
     * @var int
     */
    public $order_import_id_payment_module = 0;

    /**
     * @var int
     */
    public $order_import_id_employee = 0;

    /**
     * @var string
     */
    public $order_import_external_fulfilment = ExternalFulfilment::INTERNAL_ORDERS;

    /**
     * @var bool
     */
    public $order_import_send_emails = false;

    /**
     * @var int|null
     */
    public $order_import_api_call_timeout;

    /**
     * @var string|null
     */
    public $order_import_invoice_payment_title;

    /**
     * @var bool
     */
    public $catalog_export_active = true;

    /**
     * @var bool
     */
    public $offer_export_active = true;

    /**
     * @var bool
     */
    public $order_import_active = true;

    /**
     * @var bool
     */
    public $shipment_export_active = true;

    /**
     * @var array
     */
    public static $definition = [
        'table'     => 'ec_connection',
        'primary'   => 'id_connection',
        'multilang' => false,
        'fields'    => [
            'is_active' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'name' => [
                'type'     => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size'     => 255
            ],
            'id_shop' => [
                'type'     => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ],
            'public_key' => [
                'type'     => self::TYPE_STRING,
                'required' => true,
                'size'     => 255
            ],
            'secret_key' => [
                'type'     => self::TYPE_STRING,
                'required' => true,
                'size'     => 255
            ],
            'catalog_export_only_active' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'catalog_export_special_price' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'catalog_export_add_option_title' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'catalog_export_ean_leading_zero' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'catalog_export_skip_invalid_ean' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'catalog_export_skip_unavailable_for_order' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'order_import_id_group' => [
                'type'     => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ],
            'order_import_id_carrier' => [
                'type'     => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ],
            'order_import_id_payment_module' => [
                'type'     => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ],
            'order_import_id_employee' => [
                'type'     => self::TYPE_INT,
                'required' => true,
                'validate' => 'isUnsignedInt'
            ],
            'order_import_external_fulfilment' => [
                'type'     => self::TYPE_STRING,
                'required' => true,
                'size'     => 64
            ],
            'order_import_send_emails' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'order_import_api_call_timeout' => [
                'type'       => self::TYPE_INT,
                'required'   => false,
                'validate'   => 'isUnsignedInt',
                'allow_null' => true,
            ],
            'order_import_invoice_payment_title' => [
                'type'       => self::TYPE_STRING,
                'required'   => false,
                'size'       => 255,
                'allow_null' => true,
            ],
            'catalog_export_active' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'offer_export_active' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'order_import_active' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'shipment_export_active' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
        ]
    ];

    /**
     * Connection constructor.
     * @param null $id
     * @param null $id_lang
     * @param null $id_shop
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->order_import_id_group          = Configuration::getGlobalValue(ConfigurationKeys::EC_DEFAULT_CUSTOMER_GROUP_ID);
        $this->order_import_id_carrier        = Configuration::getGlobalValue(ConfigurationKeys::EC_DEFAULT_CARRIER_ID);
        $this->order_import_id_payment_module = Configuration::getGlobalValue(ConfigurationKeys::EC_DEFAULT_PAYMENT_MODULE_ID);
        parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * @return bool
     */
    public static function createDbTable()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`(
                    `id_connection` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',
                    `name` VARCHAR(255) NOT NULL,
                    `id_shop` INT(11) UNSIGNED NOT NULL,
                    `public_key` VARCHAR(255) NOT NULL,
                    `secret_key` VARCHAR(255) NOT NULL,
                    `catalog_export_only_active` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `catalog_export_special_price` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `catalog_export_add_option_title` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `catalog_export_ean_leading_zero` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `catalog_export_skip_invalid_ean` TINYINT(1) NOT NULL DEFAULT \'0\',
                    `catalog_export_skip_unavailable_for_order` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `order_import_id_group` INT(11) UNSIGNED NOT NULL,
                    `order_import_id_carrier` INT(11) UNSIGNED NOT NULL,
                    `order_import_id_payment_module` INT(11) UNSIGNED NOT NULL,
                    `order_import_id_employee` INT(11) UNSIGNED NOT NULL,
                    `order_import_external_fulfilment` VARCHAR(64) NOT NULL,
                    `order_import_send_emails` TINYINT(1) NOT NULL DEFAULT \'0\',
                    `order_import_api_call_timeout` INT(11) UNSIGNED NULL DEFAULT NULL,
                    `order_import_invoice_payment_title` VARCHAR(255) NULL DEFAULT NULL,
                    `catalog_export_active` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `offer_export_active` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `order_import_active` TINYINT(1) NOT NULL DEFAULT \'1\',
                    `shipment_export_active` TINYINT(1) NOT NULL DEFAULT \'1\',
                    PRIMARY KEY (`id_connection`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')
            ;
    }

    /**
     * Version 3.1.0 database migration.
     * @return bool
     */
    public static function addDbFieldOrderImportIdEmployee()
    {
        return Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . self::$definition['table'] . '` ADD COLUMN `order_import_id_employee` INT(11) UNSIGNED NOT NULL AFTER `order_import_id_payment_module`');
    }

    /**
     * Version 3.1.17 database migration.
     * @return bool
     */
    public static function addDbFieldCatalogExportUnavailableForOrder()
    {
        return Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . self::$definition['table'] . '` ADD COLUMN `catalog_export_skip_unavailable_for_order` TINYINT(1) NOT NULL DEFAULT \'1\' AFTER `catalog_export_skip_invalid_ean`');
    }

    /**
     * Version 3.1.18 database migration.
     * @return bool
     */
    public static function addDbFieldOrderImportApiCallTimeout()
    {
        return Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . self::$definition['table'] . '` ADD COLUMN `order_import_api_call_timeout` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `order_import_send_emails`');
    }

    /**
     * Version 3.1.30 database migration.
     * @return bool
     */
    public static function addDbFieldOrderImportInvoicePaymentTitle()
    {
        return Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . self::$definition['table'] . '` ADD COLUMN `order_import_invoice_payment_title` VARCHAR(255) NULL DEFAULT NULL AFTER `order_import_api_call_timeout`');
    }

    /**
     * Version 3.1.33 database migration.
     * @return bool
     */
    public static function addDbFieldsActiveProcesses()
    {
        return Db::getInstance()->execute('ALTER TABLE  `' . _DB_PREFIX_ . self::$definition['table'] . '`' .
            ' ADD COLUMN `catalog_export_active` TINYINT(1) NOT NULL DEFAULT \'1\' AFTER `order_import_invoice_payment_title`,' .
            ' ADD COLUMN `offer_export_active` TINYINT(1) NOT NULL DEFAULT \'1\' AFTER `catalog_export_active`,' .
            ' ADD COLUMN `order_import_active` TINYINT(1) NOT NULL DEFAULT \'1\' AFTER `offer_export_active`,' .
            ' ADD COLUMN `shipment_export_active` TINYINT(1) NOT NULL DEFAULT \'1\' AFTER `order_import_active`'
        );
    }

    /**
     * @return bool
     */
    public static function removeDbTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`');
    }

    /**
     * @param int $orderImportIdCarrier
     * @return Connection[]
     */
    public static function getListByOrderImportIdCarrier(int $orderImportIdCarrier)
    {
        $where = '`order_import_id_carrier` = ' . intval($orderImportIdCarrier);
        return self::getList($where);
    }

    /**
     * @return Connection[]
     */
    public static function getListActive()
    {
        $where = '`is_active` = 1';
        return self::getList($where);
    }

    /**
     * @param int $idCarrierOld
     * @param int $idCarrierNew
     */
    public static function updateCarrier(int $idCarrierOld, int $idCarrierNew)
    {
        $connectionsToUpdate = self::getListByOrderImportIdCarrier($idCarrierOld);
        foreach ($connectionsToUpdate as $connectionToUpdate)
        {
            try {
                $connectionToUpdate->order_import_id_carrier = $idCarrierNew;
                $connectionToUpdate->save(true);
            } catch (PrestaShopException $e) {
                // TODO: log
            }
        }
    }
}
