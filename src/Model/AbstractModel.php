<?php

namespace EffectConnect\Marketplaces\Model;

use Db;
use ObjectModel;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Class AbstractModel
 * @package EffectConnect\Marketplaces\Model
 */
class AbstractModel extends ObjectModel
{
    /**
     * @param string $where
     * @param int $limit
     * @return array
     */
    protected static function getList(string $where, int $limit = 0)
    {
        try {
            $list = Db::getInstance()->executeS('
                SELECT *
                FROM `' . _DB_PREFIX_ . static::$definition['table'] . '`
                WHERE ' . $where . '
                ' . ($limit > 0 ? 'LIMIT ' . intval($limit) : '')
            );
        } catch (PrestaShopDatabaseException $e) {
            return [];
        }

        try {
            return ObjectModel::hydrateCollection(static::class, $list);
        } catch (PrestaShopException $e) {
            return [];
        }
    }
}