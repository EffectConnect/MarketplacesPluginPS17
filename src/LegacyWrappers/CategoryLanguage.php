<?php

namespace EffectConnect\Marketplaces\LegacyWrappers;

use Category;
use Exception;
use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;

/**
 * Class CategoryLanguage is a wrapper for the PrestaShop legacy Category class including multiple instances from this class by language.
 * @package EffectConnect\Marketplaces\Object
 */
class CategoryLanguage
{
    /**
     * @var CategoryDataProvider
     */
    protected $_categoryDataProvider;

    /**
     * @var Category[]
     */
    protected $_categoryData = [];

    /**
     * CategoryLanguage constructor.
     * @param CategoryDataProvider $categoryDataProvider
     */
    public function __construct(
        CategoryDataProvider $categoryDataProvider
    ) {
        $this->_categoryDataProvider = $categoryDataProvider;
    }

    /**
     * @param int $idCategory
     * @param int $idShop
     * @param array $idsLang
     */
    public function loadCategoryData(int $idCategory, int $idShop, array $idsLang)
    {
        foreach ($idsLang as $idLang) {
            try {
                $category = $this->_categoryDataProvider->getCategory($idCategory, $idLang, $idShop);
            } catch (Exception $e) {
                continue;
            }
            if (!is_null($category->id)) {
                $this->_categoryData[$idLang] = $category;
            }
        }
    }

    /**
     * @param int|null $idLang
     * @return Category
     */
    public function getCategory(int $idLang = null)
    {
        if (isset($this->_categoryData[$idLang])) {
            return $this->_categoryData[$idLang];
        }
        return reset($this->_categoryData);
    }

    /**
     * @return Category[]
     */
    public function getCategories()
    {
        return $this->_categoryData;
    }
}