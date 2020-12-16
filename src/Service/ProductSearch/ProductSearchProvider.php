<?php

namespace EffectConnect\Marketplaces\Service\ProductSearch;

use PrestaShopException;
use Product;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use Shop;

/**
 * Class ProductSearchProvider
 * @package EffectConnect\Marketplaces\Service\ProductSearch
 */
class ProductSearchProvider implements ProductSearchProviderInterface
{
    /**
     * @param ProductSearchContext $context
     * @param ProductSearchQuery $query
     * @return ProductSearchResult
     */
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $result = new ProductSearchResult();

        $productsCount = count($this->getProductsOrCount($context, $query, 'count'));

        if ($productsCount > 0) {
            $products = $this->getProductsOrCount($context, $query, 'products');
            $result->setProducts($products);
        }

        $result->setTotalProductsCount($productsCount);

        return $result;
    }

    /**
     * @param ProductSearchContext $context
     * @param ProductSearchQuery $query
     * @param string $type
     * @return array
     */
    protected function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        // The legacy function getProducts has no option to provide a shop (it will fetch the shop from the context).
        // So before using this function in shop context, make sure to call InitContext->setShop()
        return Product::getProducts(
            $context->getIdLang(),
            $query->getPage(),
            $type === 'products' ? $query->getResultsPerPage() : 0,
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay(),
            false,
            $context->getOnlyActiveProducts()
        );
    }
}
