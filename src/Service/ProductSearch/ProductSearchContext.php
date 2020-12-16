<?php

namespace EffectConnect\Marketplaces\Service\ProductSearch;

/**
 * Class ProductSearchContext
 * @package EffectConnect\Marketplaces\Service\ProductSearch
 */
class ProductSearchContext
{
    /**
     * @var int the Language id
     */
    protected $_idLang;

    /**
     * @var boolean whether to search for active products only
     */
    protected $_onlyActiveProducts;

    /**
     * @param int $idLang
     * @return $this
     */
    public function setIdLang(int $idLang)
    {
        $this->_idLang = $idLang;

        return $this;
    }

    /**
     * @return int the Product Search Language id
     */
    public function getIdLang()
    {
        return $this->_idLang;
    }

    /**
     * @param bool $onlyActiveProducts
     * @return $this
     */
    public function setOnlyActiveProducts(bool $onlyActiveProducts)
    {
        $this->_onlyActiveProducts = $onlyActiveProducts;

        return $this;
    }

    /**
     * @return bool whether Product Search searches for active products only
     */
    public function getOnlyActiveProducts()
    {
        return $this->_onlyActiveProducts;
    }
}
