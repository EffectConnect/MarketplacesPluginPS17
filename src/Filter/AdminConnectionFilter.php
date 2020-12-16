<?php

namespace EffectConnect\Marketplaces\Filter;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class AdminConnectionFilter
 * @package EffectConnect\Marketplaces\Filter
 */
final class AdminConnectionFilter extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit'     => 10,
            'offset'    => 0,
            'orderBy'   => 'id_connection',
            'sortOrder' => 'asc',
            'filters'   => [],
        ];
    }
}
