<?php

namespace EffectConnect\Marketplaces\Service\ProductSearch;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;

/**
 * Will define the query to execute in order to retrieve the list of products.
 */
interface ProductSearchProviderInterface
{
    /**
     * @param ProductSearchContext $context
     * @param ProductSearchQuery $query
     * @return ProductSearchResult
     */
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    );
}
