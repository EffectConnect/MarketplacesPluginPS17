<?php

namespace EffectConnect\Marketplaces\Service\Transformer;

use Category;
use DOMException;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Exception\InitContextFailedException;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use EffectConnect\Marketplaces\Helper\StringHelper;
use EffectConnect\Marketplaces\Helper\VenditHelper;
use EffectConnect\Marketplaces\Model\VenditWarehouse;
use EffectConnect\Marketplaces\Service\InitContext;
use EffectConnect\Marketplaces\Service\ProductSearch\ProductSearchProvider;
use EffectConnect\Marketplaces\Exception\FileCreationFailedException;
use EffectConnect\Marketplaces\Exception\ProductLoadException;
use EffectConnect\Marketplaces\Helper\FileHelper;
use EffectConnect\Marketplaces\Helper\XmlHelper;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\LegacyWrappers\CategoryLanguage;
use Exception;
use LogicException;
use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\Feature\FeatureDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use EffectConnect\Marketplaces\Service\ProductSearch\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use Product;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CatalogExportTransformer
 * @package EffectConnect\Marketplaces\Service\Transformer
 */
class CatalogExportTransformer extends AbstractTransformer
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_CATALOG;

    /**
     * The directory where the catalog XML needs to be generated.
     */
    protected const CONTENT_TYPE = 'catalog';

    /**
     * The root element for the XML file containing the catalog.
     */
    protected const XML_ROOT_ELEMENT = 'products';

    /**
     * The page size when iterating trough the products.
     */
    protected const PAGE_SIZE = 50;

    /**
     * @var Configuration
     */
    protected $_configuration;

    /**
     * @var ProductDataProvider
     */
    protected $_productDataProvider;

    /**
     * @var FeatureDataProvider
     */
    protected $_featureDataProvider;

    /**
     * @var CategoryDataProvider
     */
    protected $_categoryDataProvider;

    /**
     * @var CategoryLanguage
     */
    protected $_categoryLanguage;

    /**
     * @var ProductSearchProvider
     */
    protected $_productSearchProvider;

    /**
     * Contains an array of processed EANs in order to keep track of duplicates.
     * @var array
     */
    protected $_processedEANs = [];

    /**
     * Contains an array by language ID with the currently loaded product for all languages to export.
     * @var Product[]
     */
    protected $_productLanguageData = [];

    /**
     * Contains an array by language ID with the currently loaded product attributes for all languages to export.
     * @var array
     */
    protected $_productAttributesResumeLanguageData = [];

    /**
     * @var array
     */
    protected $_categoryLanguageData = [];

    /**
     * @var array
     */
    protected $_featureLanguageData = [];

    /**
     * @var PriceFormatter
     */
    protected $_priceFormatter;

    /**
     * @var VenditWarehouse[]
     */
    protected $_venditWarehouses = [];

    /**
     * CatalogExportTransformer constructor.
     *
     * @param InitContext $initContext
     * @param LegacyContext $legacyContext
     * @param CurrencyDataProvider $currencyDataProvider
     * @param TranslatorInterface $translator
     * @param LoggerHelper $loggerHelper
     * @param Configuration $configuration
     * @param ProductDataProvider $productDataProvider
     * @param FeatureDataProvider $featureDataProvider
     * @param CategoryDataProvider $categoryDataProvider
     * @param CategoryLanguage $categoryLanguage
     * @param ProductSearchProvider $productSearchProvider
     * @param PriceFormatter $priceFormatter
     */
    public function __construct(
        InitContext $initContext,
        LegacyContext $legacyContext,
        CurrencyDataProvider $currencyDataProvider,
        TranslatorInterface $translator,
        LoggerHelper $loggerHelper,
        Configuration $configuration,
        ProductDataProvider $productDataProvider,
        FeatureDataProvider $featureDataProvider,
        CategoryDataProvider $categoryDataProvider,
        CategoryLanguage $categoryLanguage,
        ProductSearchProvider $productSearchProvider,
        PriceFormatter $priceFormatter
    ) {
        $this->_configuration         = $configuration;
        $this->_productDataProvider   = $productDataProvider;
        $this->_featureDataProvider   = $featureDataProvider;
        $this->_categoryDataProvider  = $categoryDataProvider;
        $this->_categoryLanguage      = $categoryLanguage;
        $this->_productSearchProvider = $productSearchProvider;
        $this->_priceFormatter        = $priceFormatter;
        $this->_logger                = $loggerHelper::createLogger(static::LOGGER_PROCESS);
        parent::__construct($initContext, $legacyContext, $currencyDataProvider, $translator);
    }

    /**
     * @param Connection $connection
     * @param array $productIdsToExport
     * @return false|string
     * @throws FileCreationFailedException
     * @throws InitContextFailedException
     */
    public function buildCatalogXml(Connection $connection, array $productIdsToExport = [])
    {
        // TODO: check for valid shop id (and languages)?
        $this->_processedEANs = [];

        $this->init($connection);
        $this->loadFeatureData();
        $this->loadVenditWarehouses();

        $fileLocation = FileHelper::generateFile(static::CONTENT_TYPE, $this->getShopId());
        try {
            $xmlHelper = XmlHelper::startTransaction($fileLocation, static::XML_ROOT_ELEMENT);
        } catch (DOMException $e) {
            throw new FileCreationFailedException($fileLocation, $e->getMessage());
        }

        $context = (new ProductSearchContext())
            ->setIdLang($this->getDefaultLanguage())
            ->setOnlyActiveProducts(boolval($this->getConnection()->catalog_export_only_active))
        ;

        $page              = 0;
        $totalProductCount = 0;
        do
        {
            $query = (new ProductSearchQuery())
                ->setPage($page)
                ->setResultsPerPage(static::PAGE_SIZE);

            $result = $this->_productSearchProvider->runQuery(
                $context,
                $query
            );

            foreach ($result->getProducts() as $productRaw)
            {
                $idProduct = intval($productRaw['id_product']);

                // Do we only need to sync specific products?
                if (count($productIdsToExport) > 0 && !in_array($idProduct, $productIdsToExport)) {
                    continue;
                }

                try {
                    $this->loadProductLanguageData($idProduct);
                    $this->loadProductAttributesResumeLanguageData($idProduct);
                    $product      = $this->getProductLanguageData($this->getDefaultLanguage());
                    $productArray = $this->getProductArray($product);
                } catch (ProductLoadException $e) {
                    $this->_logger->error('Skipping product because it could not be loaded.', [
                        'process'    => static::LOGGER_PROCESS,
                        'message'    => $e->getMessage(),
                        'id_product' => $idProduct,
                        'connection' => [
                            'id' => $this->getConnection()->id
                        ]
                    ]);
                    continue;
                }

                // Exclude virtual products
                if ($product->getType() === Product::PTYPE_VIRTUAL) {
                    $this->_logger->info('Skipping product because virtual products are not supported.', [
                        'process'    => static::LOGGER_PROCESS,
                        'id_product' => $idProduct,
                        'connection' => [
                            'id' => $this->getConnection()->id
                        ]
                    ]);
                    continue;
                }

                if (empty($productArray)) {
                    $this->_logger->info('Skipping product because it is empty.', [
                        'process'    => static::LOGGER_PROCESS,
                        'id_product' => $idProduct,
                        'connection' => [
                            'id' => $this->getConnection()->id
                        ]
                    ]);
                    continue;
                }

                try {
                    $xmlHelper->append($productArray, 'product');
                } catch (DOMException $e) {
                    $this->_logger->error('Skipping product because it could not be converted to XML.', [
                        'process'    => static::LOGGER_PROCESS,
                        'message'    => $e->getMessage(),
                        'connection' => [
                            'id' => $this->getConnection()->id
                        ],
                        'product'    => $productArray
                    ]);
                    continue;
                }

                $totalProductCount++;
            }

            $page += static::PAGE_SIZE;
        }
        while (count($result->getProducts()) > 0);

        $xmlHelper->endTransaction();

        // Don't return file path if it contains no products.
        if ($totalProductCount == 0)
        {
            $this->_logger->info('No product to export.', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            return false;
        }

        $this->_logger->info($totalProductCount . ' products processed.', [
            'process'    => static::LOGGER_PROCESS,
            'connection' => [
                'id' => $connection->id
            ]
        ]);

        if (count($productIdsToExport) > 0) {
            $this->_logger->info('Only queued items were exported.', [
                'process'     => static::LOGGER_PROCESS,
                'product_ids' => $productIdsToExport,
                'connection'  => [
                    'id' => $connection->id
                ]
            ]);
        }

        return realpath($fileLocation);
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductOptionsArray(Product $product)
    {
        if ($product->hasCombinations())
        {
            return $this->getProductOptionsArrayFromCombinationProduct($product);
        }
        else
        {
            return $this->getProductOptionsArrayFromSimpleProduct($product);
        }
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductArray(Product $product)
    {
        $productExport = [];

        $productOptions = $this->getProductOptionsArray($product);
        if (count($productOptions) > 0)
        {
            $productExport = [
                'identifier' => $product->id,
                'options'    => [
                    'option' => $productOptions
                ]
            ];

            // Brand
            if (!empty($product->manufacturer_name)) {
                $productExport['brand'] = $product->manufacturer_name;
            }

            // Categories
            $categories = $this->getCategories($product);
            if (count($categories) > 0)
            {
                $productExport['categories'] = $categories;
            }
        }

        return $productExport;
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductOptionsArrayFromSimpleProduct(Product $product)
    {
        $productOptionExport = $this->getProductOptionArray($this->getProductIdentifier(intval($product->id)), strval($product->reference), strval($product->ean13), floatval($product->wholesale_price), $product);

        if (empty($productOptionExport)) {
            return [];
        }

        $images = $this->getProductImages($product, $product->getImages($this->getDefaultLanguage()));
        if (count($images) > 0) {
            $productOptionExport['images']['image'] = $images;
        }

        return [$productOptionExport];
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductOptionsArrayFromCombinationProduct(Product $product)
    {
        $productOptionsExport = [];

        $combinationsArray = $this->getProductAttributesResumeLanguageData($this->getDefaultLanguage());
        $combinationImages = $product->getCombinationImages($this->getDefaultLanguage());
        foreach ($combinationsArray as $combinationArray)
        {
            $idCombination       = intval($combinationArray['id_product_attribute']);
            $identifier          = $this->getProductIdentifier(intval($product->id), $idCombination);
            $productOptionExport = $this->getProductOptionArray($identifier, strval($combinationArray['reference']), strval($combinationArray['ean13']), floatval($combinationArray['wholesale_price']), $product, $idCombination);

            if (empty($productOptionExport)) {
                continue;
            }

            $images = $this->getProductImages($product, $combinationImages[$idCombination] ?? []);
            if (count($images) > 0) {
                $productOptionExport['images']['image'] = $images;
            }

            $productOptionsExport[] = $productOptionExport;
        }

        return $productOptionsExport;
    }

    /**
     * @param string $identifier
     * @param string $sku
     * @param string $ean
     * @param float $wholeSalePrice
     * @param Product $product
     * @param int|null $idCombination
     * @return array
     */
    protected function getProductOptionArray(string $identifier, string $sku, string $ean, float $wholeSalePrice, Product $product, int $idCombination = null)
    {
        // Product titles (by language)
        $titles = $this->getProductTitles($idCombination);
        if (count($titles) == 0)
        {
            $this->_logger->warning('Skipping product because it has no title.', [
                'process'    => static::LOGGER_PROCESS,
                'product'    => [
                    'identifier' => $identifier,
                    'sku'        => $sku,
                    'ean'        => $ean
                ],
                'connection' => [
                    'id' => $this->getConnection()->id
                ]
            ]);
            return [];
        }

        $productOptionExport = [
            'identifier'    => $identifier,
            'sku'           => $sku,
            'stock'         => $this->getStock($product, $idCombination),
            'cost'          => number_format($this->convertPriceToEuro($wholeSalePrice), 2, '.', ''),
            'titles'        => [
                'title' => $titles
            ],
        ];

        // Product prices
        $price                        = $this->getPrice($product, $idCombination);
        $priceOriginal                = $this->getPriceOriginal($product, $idCombination);
        $productOptionExport['price'] = number_format($price, 2, '.', '');
        if ($priceOriginal !== null && $priceOriginal > $price) {
            $productOptionExport['priceOriginal'] = number_format($priceOriginal, 2, '.', '');
        }

        // EAN
        $modifiedEan = $this->getEAN($ean);
        if (!$this->validateEAN($modifiedEan))
        {
            // Skip invalid EAN or export product without EAN?
            if ($this->getConnection()->catalog_export_skip_invalid_ean)
            {
                $this->_logger->warning('Skipping product because of invalid EAN.', [
                    'process'    => static::LOGGER_PROCESS,
                    'product'    => [
                        'identifier' => $identifier,
                        'sku'        => $sku,
                        'ean'        => $ean
                    ],
                    'connection' => [
                        'id' => $this->getConnection()->id
                    ]
                ]);
                return [];
            }
            $modifiedEan = '';
        }

        // Skip products that are unavailable for order
        if ($this->getConnection()->catalog_export_skip_unavailable_for_order && !$product->available_for_order)
        {
            $this->_logger->warning('Skipping product because it is unavailable for order.', [
                'process'    => static::LOGGER_PROCESS,
                'product'    => [
                    'identifier' => $identifier,
                    'sku'        => $sku,
                    'ean'        => $ean
                ],
                'connection' => [
                    'id' => $this->getConnection()->id
                ]
            ]);
            return [];
        }

        // We don't include EAN field in export when EAN is empty
        if (!empty($modifiedEan))
        {
            // Never export duplicate EANs
            if (in_array($modifiedEan, $this->_processedEANs))
            {
                $this->_logger->warning('Skipping product because of duplicate EAN.', [
                    'process'    => static::LOGGER_PROCESS,
                    'product'    => [
                        'identifier' => $identifier,
                        'sku'        => $sku,
                        'ean'        => $ean
                    ],
                    'connection' => [
                        'id' => $this->getConnection()->id
                    ]
                ]);
                return [];
            }

            $productOptionExport['ean'] = $modifiedEan;
            $this->_processedEANs[]     = $modifiedEan;
        }

        // Product descriptions (by language)
        $descriptions = $this->getProductDescriptions();
        if (count($descriptions) > 0) {
            $productOptionExport['descriptions']['description'] = $descriptions;
        }

        // Product urls (by language)
        $urls = $this->getProductUrls($idCombination);
        if (count($urls) > 0) {
            $productOptionExport['urls']['url'] = $urls;
        }

        // Attributes
        $attributes = $this->getProductAttributes($product, $idCombination);
        if (count($attributes) > 0)
        {
            $productOptionExport['attributes']['attribute'] = $attributes;
        }

        // Delivery time
        $deliveryTime = $this->getDeliveryTime($product);
        if (!empty($deliveryTime))
        {
            $productOptionExport['deliveryTime'] = $deliveryTime;
        }

        return $productOptionExport;
    }

    /**
     * @param Product $product
     * @param array $images
     * @return array
     */
    protected function getProductImages(Product $product, array $images)
    {
        $productImagesExport = [];

        if (count($images) > 0)
        {
            $sortOrder = 1;
            foreach($images as $image)
            {
                // TODO: test this when Search Friendly Urls are enabled (first check whether frontend images work when enabled)
                if (is_array($product->link_rewrite)) {
                    $linkRewrite = $product->link_rewrite[$this->getDefaultLanguage()] ?? reset($product->link_rewrite);
                } else {
                    $linkRewrite = $product->link_rewrite;
                }

                $imageUrl = $this->_legacyContext->getContext()->link->getImageLink($linkRewrite, $image['id_image']);
                $productImagesExport[$imageUrl] = [
                    'url'   => $imageUrl,
                    'order' => $sortOrder,
                ];
                $sortOrder++;
                if ($sortOrder > 10)
                {
                    $this->_logger->warning('Skipped some images, because maximum allowed number of images is limited to 10.', [
                        'process'     => static::LOGGER_PROCESS,
                        'image_count' => count($images),
                        'product'     => [
                            'id'  => $product->id,
                            'sku' => $product->reference,
                            'ean' => $product->ean13,
                        ],
                        'connection' => [
                            'id' => $this->getConnection()->id
                        ]
                    ]);
                    break;
                }
            }
        }

        return array_values($productImagesExport); // Array key $imageUrl is used to keep images unique
    }

    /**
     * TODO: load this shizzle into a product object?
     *   Used for: getProductTitles, getProductDescriptions, getProductUrls
     *
     * @param int $idProduct
     * @throws ProductLoadException
     */
    protected function loadProductLanguageData(int $idProduct)
    {
        $this->_productLanguageData = [];
        foreach (array_keys($this->_languageIsoCodeById) as $idLang)
        {
            try {
                $product = $this->_productDataProvider->getProduct($idProduct, true, $idLang, $this->getShopId());
            } catch (LogicException $e) {
                throw new ProductLoadException('Could not load language data');
            }
            $this->_productLanguageData[$idLang] = $product;
        }
    }

    /**
     * @param int $idLang
     * @return Product
     * @throws ProductLoadException
     */
    protected function getProductLanguageData(int $idLang)
    {
        if (!isset($this->_productLanguageData[$idLang])) {
            throw new ProductLoadException('Language data not present');
        }
        return $this->_productLanguageData[$idLang];
    }

    /**
     * @param int $idProduct
     * @throws ProductLoadException
     */
    protected function loadProductAttributesResumeLanguageData(int $idProduct)
    {
        $this->_productAttributesResumeLanguageData = [];
        foreach (array_keys($this->_languageIsoCodeById) as $idLang)
        {
            $productLang      = $this->getProductLanguageData($idLang); // Product data should always be present - throws ProductLoadException if not.
            $attributesResume = $productLang->getAttributesResume($idLang); // Attributes data is optional - not all product have combinations.
            if (!is_array($attributesResume)) {
                continue;
            }
            foreach($attributesResume as $attributeResume)
            {
                $this->_productAttributesResumeLanguageData[$idLang][$attributeResume['id_product_attribute']] = $attributeResume;
            }
        }
    }

    /**
     * @param int $idLang
     * @param int|null $idCombination
     * @return mixed
     * @throws ProductLoadException
     */
    protected function getProductAttributesResumeLanguageData(int $idLang, int $idCombination = null)
    {
        if (is_null($idCombination)) {
            if (!isset($this->_productAttributesResumeLanguageData[$idLang])) {
                throw new ProductLoadException('Language data for attributes not present');
            }
            return $this->_productAttributesResumeLanguageData[$idLang];
        }
        if (!isset($this->_productAttributesResumeLanguageData[$idLang][$idCombination])) {
            throw new ProductLoadException('Language data for attributes not present');
        }
        return $this->_productAttributesResumeLanguageData[$idLang][$idCombination];
    }

    /**
     * TODO: use objects for this instead of arrays?
     *   And also use getFeatureData (like getProductLanguageData)?
     */
    protected function loadFeatureData()
    {
        foreach (array_keys($this->_languageIsoCodeById) as $languageId)
        {
            foreach ($this->_featureDataProvider::getFeatures($languageId) as $feature)
            {
                $featureId     = intval($feature['id_feature']);
                $featureValues = $this->_featureDataProvider::getFeatureValuesWithLang($languageId, $featureId, true);
                foreach ($featureValues as $featureValue)
                {
                    $featureValueId                             = intval($featureValue['id_feature_value']);
                    $feature['feature_values'][$featureValueId] = $featureValue;
                }
                $this->_featureLanguageData[$languageId][$featureId] = $feature;
            }
        }
    }

    /**
     * @param int|null $idCombination
     * @return array
     */
    protected function getProductTitles(int $idCombination = null)
    {
        $titles = [];

        foreach ($this->_languageIsoCodeById as $languageId => $languageIsoCode)
        {
            try {
                $productLang = $this->getProductLanguageData($languageId);
            } catch (ProductLoadException $e) {
                continue;
            }

            if (empty($productLang->name)) {
                continue;
            }

            $productTitle = strval($productLang->name);

            if (boolval($this->getConnection()->catalog_export_add_option_title) === true && intval($idCombination) > 0)
            {
                try {
                    $productAttributesResumeLang = $this->getProductAttributesResumeLanguageData($languageId, $idCombination);
                    $productOptionTitle          = $productAttributesResumeLang['attribute_designation'] ?? '';
                } catch (ProductLoadException $e) {
                    $productOptionTitle = '';
                }

                if (!empty($productOptionTitle)) {
                    $productTitle .= ' (' . $productOptionTitle . ')';
                }
            }

            if (!empty($productLang->name)) {
                $titles[] = [
                    '_attributes' => ['language' => $languageIsoCode],
                    '_cdata'      => $productTitle
                ];
            }
        }

        return $titles;
    }

    /**
     * Checks if shop has Vendit warehouse support.
     *
     * @return void
     */
    protected function loadVenditWarehouses()
    {
        if (VenditHelper::hasWarehouseSupport()) {
            $this->_venditWarehouses = VenditHelper::getWarehouses();
        }
    }

    /**
     * @return array
     */
    protected function getProductDescriptions()
    {
        $descriptions = [];

        foreach ($this->_languageIsoCodeById as $languageId => $languageIsoCode)
        {
            try {
                $productLang = $this->getProductLanguageData($languageId);
            } catch (ProductLoadException $e) {
                continue;
            }

            $productDescription = trim($productLang->description);
            $productDescriptionShort = trim($productLang->description_short);
            if (!empty($productDescription)) {
                $descriptions[] = [
                    '_attributes' => ['language' => $languageIsoCode],
                    '_cdata'      => $productDescription
                ];
            } elseif(!empty($productDescriptionShort)) {
                $descriptions[] = [
                    '_attributes' => ['language' => $languageIsoCode],
                    '_cdata'      => $productDescriptionShort
                ];
            }
        }

        return $descriptions;
    }

    /**
     * @param int|null $idCombination
     * @return array
     */
    protected function getProductUrls(int $idCombination = null)
    {
        $urls = [];

        foreach ($this->_languageIsoCodeById as $languageId => $languageIsoCode)
        {
            try {
                $productLang = $this->getProductLanguageData($languageId);
            } catch (ProductLoadException $e) {
                continue;
            }

            try {
                $url = $this->_legacyContext->getContext()->link->getProductLink($productLang, null, null, null, $languageId, $this->getShopId(), $idCombination);
            } catch (Exception $e) {
                $url = '';
            }
            if (!empty($url)) {
                $urls[] = [
                    '_attributes' => ['language' => $languageIsoCode],
                    '_value'      => $url,
                ];
            }
        }

        return $urls;
    }

    /**
     * @param Product $product
     * @param int|null $idCombination
     * @return array
     */
    protected function getProductAttributes(Product $product, int $idCombination = null)
    {
        // Get fixed attributes
        $attributesExport = array_merge(
            $this->getProductAttributeAvailableForOrder($product),
            $this->getProductAttributeDimensions($product),
            $this->getProductLanguageAttributes($product),
            $this->getProductAttributeVenditWarehouse($product),
            $this->getProductAttributeCombinations($product, $idCombination)
        );

        $attributeValuesExport = [];
        $features = $product->getFeatures();

        if (count($features) > 0)
        {
            foreach($features as $feature)
            {
                $idFeature      = $feature['id_feature'];
                $idFeatureValue = $feature['id_feature_value'];

                //
                // Get attributes names
                //

                $attributeNames = [];
                foreach ($this->_languageIsoCodeById as $languageId => $languageIsoCode)
                {
                    if (!isset($this->_featureLanguageData[$languageId][$idFeature])) {
                        continue;
                    }

                    $translatedFeature = $this->_featureLanguageData[$languageId][$idFeature];
                    if (empty($translatedFeature['name'])) {
                        continue;
                    }

                    $attributeNames[] = [
                        '_attributes' => ['language' => $languageIsoCode],
                        '_cdata'      => $translatedFeature['name'],
                    ];
                }

                if (count($attributeNames) == 0) {
                    continue;
                }

                //
                // Get attribute value names
                //

                $attributeValueNames = [];
                foreach ($this->_languageIsoCodeById as $languageId => $languageIsoCode)
                {
                    if (!isset($this->_featureLanguageData[$languageId][$idFeature]['feature_values'][$idFeatureValue])) {
                        continue;
                    }

                    $translatedFeatureValue = $this->_featureLanguageData[$languageId][$idFeature]['feature_values'][$idFeatureValue];
                    if (empty($translatedFeatureValue['value'])) {
                        continue;
                    }

                    $attributeValueNames[] = [
                        '_attributes' => ['language' => $languageIsoCode],
                        '_cdata'      => $translatedFeatureValue['value'],
                    ];
                }

                if (count($attributeValueNames) == 0) {
                    continue;
                }

                $attributeValuesExport[$idFeature][$idFeatureValue] = [
                    'code'   => $idFeatureValue,
                    'names' => [
                        'name' => $attributeValueNames,
                    ],
                ];

                $attributesExport[$idFeature] = [
                    'code'   => $idFeature,
                    'names' => [
                        'name' => $attributeNames,
                    ],
                    'values' => [
                        'value' => $attributeValuesExport[$idFeature],
                    ],
                ];
            }
        }

        return array_values($attributesExport); // Make sure keys are numerical (otherwise keys will appear as XML tag)
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getCategories(Product $product)
    {
        $categoriesTreeItems = [];
        foreach ($product->getCategories() as $categoryId)
        {
            // Category tree items is no tree, it's an array with tree elements
            $categoryTreeItems = $this->getCategoryTreeItems(intval($categoryId));
            if (count($categoryTreeItems) > 0)
            {
                $categoriesTreeItems[] = $categoryTreeItems;
            }
        }

        // Merge all category's tree items into entire category tree
        return $this->getCategoryTree($categoriesTreeItems);
    }

    /**
     * @param int $idCategory
     * @return array
     */
    protected function getCategoryTreeItems(int $idCategory)
    {
        $tree = [];

        $this->_categoryLanguage->loadCategoryData($idCategory, $this->getShopId(), array_keys($this->_languageIsoCodeById));
        $category = $this->_categoryLanguage->getCategory();

        if (!($category instanceof Category)) {
            return $tree;
        }

        do
        {
            array_unshift($tree, $this->getCategoryStructure($this->_categoryLanguage));

            $idParent = intval($category->id_parent);
            if ($idParent > 0) {
                $this->_categoryLanguage->loadCategoryData($idParent, $this->getShopId(), array_keys($this->_languageIsoCodeById)); // TODO: only execute this once for each category ID?
                $category = $this->_categoryLanguage->getCategory();
            }
        }
        while ($idParent > 0 && $category instanceof Category);

        return $tree;
    }

    /**
     * @param CategoryLanguage $categoryLanguage
     * @return array
     */
    protected function getCategoryStructure(CategoryLanguage $categoryLanguage)
    {
        $titles = [
            'title' => []
        ];

        foreach ($this->_languageIsoCodeById as $idLang => $isoCode)
        {
            $category = $categoryLanguage->getCategory($idLang);
            if (!empty($category->name)) {
                $titles['title'][] = [
                    '_attributes' => ['language' => $isoCode],
                    '_cdata'      => strval($category->name),
                ];
            }
        }

        return [
            'id'     => $categoryLanguage->getCategory()->id,
            'titles' => $titles
        ];

    }

    /**
     * @param array $categoriesTreeItems
     * @return array
     */
    protected function getCategoryTree(array $categoriesTreeItems)
    {
        $categoryTree = [];

        foreach ($categoriesTreeItems as $categoryTreeItems)
        {
            $treeHead        = &$categoryTree;
            $categoryCounter = 0;

            foreach ($categoryTreeItems as $category)
            {
                $categoryId = $category['id'];

                if (isset($treeHead['category'][$categoryId])) {
                    $treeHead['category'][$categoryId]  = $treeHead['category'][$categoryId] + $category;
                } else {
                    $treeHead['category'][$categoryId]  = $category;
                }

                $categoryCounter++;
                if ($categoryCounter < count($categoryTreeItems)) {
                    if (!isset($treeHead['category'][$categoryId]['children'])) {
                        $treeHead['category'][$categoryId]['children'] = [];
                    }
                    $treeHead = &$treeHead['category'][$categoryId]['children'];
                }
            }
        }

        return $categoryTree;
    }

    /**
     * @param Product $product
     * @return string
     */
    protected function getDeliveryTime(Product $product)
    {
        // TODO: implement method
        return '';
    }

    /**
     * @param Product $product
     * @param int|null $idCombination
     * @return float
     */
    protected function getPrice(Product $product, int $idCombination = null)
    {
        $useReduction = true;
        if (!$this->getConnection()->catalog_export_special_price) {
            $useReduction = false;
        }

        $price = $product->getPrice(true, $idCombination, 6, null, false, $useReduction);
        return $this->convertPriceToEuro($price);
    }

    /**
     * @param Product $product
     * @param int|null $idCombination
     * @return float|null
     */
    protected function getPriceOriginal(Product $product, int $idCombination = null)
    {
        if (!$this->getConnection()->catalog_export_special_price) {
            return null;
        }

        $price = $product->getPrice(true, $idCombination, 6, null, false, false);
        return $this->convertPriceToEuro($price);
    }

    /**
     * @param float|null $price
     * @return float|null
     */
    protected function convertPriceToEuro(float $price = null)
    {
        if (is_null($price)) {
            return $price;
        }

        // No euro currency present in shop, we can't convert price.
        if (is_null($this->_currencyEuro)) {
            return $price;
        }

        // Shop currency is EUR, no need to convert price.
        if ($this->_currencyEuro->id == $this->_currencyShop->id) {
            return $price;
        }

        return $this->_priceFormatter->convertAmount($price, $this->_currencyEuro->id);
    }

    /**
     * @param string $ean
     * @return string
     */
    protected function getEAN(string $ean)
    {
        // Add leading zero in case EAN consists of 12 characters?
        if ($this->getConnection()->catalog_export_ean_leading_zero && strlen($ean) === 12) {
            $ean = str_pad($ean, 13, '0', STR_PAD_LEFT);
        }

        return $ean;
    }

    /**
     * @param Product $product
     * @param int|null $idCombination
     * @return int
     */
    protected function getStock(Product $product, int $idCombination = null)
    {
        return max(0, min(Product::getQuantity($product->id, $idCombination), 9999));
    }

    /**
     * @param string $ean
     * @return bool
     */
    protected function validateEAN(string $ean)
    {
        return (1 === preg_match('~^[0-9]{13,14}$~', $ean));
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductAttributeDimensions(Product $product)
    {
        $attributesExport = [];

        $dimensionAttributes = ['width', 'height', 'depth', 'weight'];
        foreach ($dimensionAttributes as $dimensionAttribute)
        {
            $attributeValue = $product->{$dimensionAttribute};

            $attributeValueNames = [];
            foreach ($this->_languages as $languageId => $language)
            {
                $attributeValueNames[] = [
                    '_attributes' => ['language' => $language['iso_code']],
                    '_cdata'      => $attributeValue,
                ];
            }

            $attributeNames = [];
            foreach ($this->_languages as $languageId => $language)
            {
                $attributeNames[] = [
                    '_attributes' => ['language' => $language['iso_code']],
                    '_cdata'      => $this->_translator->trans(ucfirst($dimensionAttribute), [], 'Modules.Effectconnectmarketplaces.Admin', $language['locale']),
                ];
            }

            $attributeValuesExport = [
                'code'   => $attributeValue,
                'names' => [
                    'name' => $attributeValueNames,
                ],
            ];

            $attributeCode = 'base_' . $dimensionAttribute;
            $attributesExport[$attributeCode] = [
                'code'   => $attributeCode,
                'names' => [
                    'name' => $attributeNames,
                ],
                'values' => [
                    'value' => $attributeValuesExport,
                ],
            ];
        }

        return $attributesExport;
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductLanguageAttributes(Product $product)
    {
        $attributesExport = [];

        $languageAttributes = ['description_short'];
        foreach ($languageAttributes as $languageAttribute)
        {
            $attributeValueCode = $product->id . '_' . $languageAttribute . '_' . StringHelper::slugify(trim($product->{$languageAttribute}));

            $attributeValueNames = [];
            $languagesWithValues = [];
            foreach ($this->_languages as $languageId => $language)
            {
                try {
                    $productLang    = $this->getProductLanguageData($languageId);
                    $attributeValue = trim($productLang->{$languageAttribute});
                } catch (ProductLoadException $e) {
                    $attributeValue = '';
                }

                if (empty($attributeValue)) {
                    continue;
                }

                $languagesWithValues[] = $languageId;
                $attributeValueNames[] = [
                    '_attributes' => ['language' => $language['iso_code']],
                    '_cdata'      => $attributeValue,
                ];
            }

            $attributeNames = [];
            foreach ($this->_languages as $languageId => $language)
            {
                if (in_array($languageId, $languagesWithValues)) {
                    $attributeNames[] = [
                        '_attributes' => ['language' => $language['iso_code']],
                        '_cdata'      => $this->_translator->trans(ucfirst($languageAttribute), [], 'Modules.Effectconnectmarketplaces.Admin', $language['locale']),
                    ];
                }
            }

            if (count($attributeNames) === 0) {
                return $attributesExport;
            }

            $attributeValuesExport = [
                'code'   => $attributeValueCode,
                'names' => [
                    'name' => $attributeValueNames,
                ],
            ];

            $attributeCode = 'base_' . $languageAttribute;
            $attributesExport[$attributeCode] = [
                'code'   => $attributeCode,
                'names' => [
                    'name' => $attributeNames,
                ],
                'values' => [
                    'value' => $attributeValuesExport,
                ],
            ];
        }

        return $attributesExport;
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductAttributeVenditWarehouse(Product $product)
    {
        $attributesExport = [];

        if (!VenditHelper::hasWarehouseSupport()) {
            return $attributesExport;
        }

        $venditStockData = VenditHelper::getProductStock($product->id);

        /** @var int $warehouseId */
        foreach ($venditStockData as $warehouseId => $venditStock) {
            if (!isset($this->_venditWarehouses[$warehouseId])) {
                continue;
            }

            $warehouse = $this->_venditWarehouses[$warehouseId];

            $attributeNames = [];
            $attributeValueNames = [];

            $warehouseAttributeName = "Vendit warehouse " . $warehouse->getName() . " stock";
            $warehouseAttributeCode = "vendit_warehouse_" . $warehouse->getId() . "_" . StringHelper::slugify($warehouse->getName()) . "_stock";

            foreach ($this->_languages as $languageId => $language) {
                $attributeNames[] = [
                    '_attributes' => ['language' => $language['iso_code']],
                    '_cdata' => $warehouseAttributeName,
                ];

                $attributeValueNames[] = [
                    '_attributes' => ['language' => $language['iso_code']],
                    '_cdata' => (string)$venditStock->getQuantity(),
                ];
            }

            $attributeValuesExport = [
                'code' => $venditStock->getQuantity(),
                'names' => [
                    'name' => $attributeValueNames,
                ],
            ];

            $attributesExport[$warehouseAttributeCode] = [
                'code' => $warehouseAttributeCode,
                'names' => [
                    'name' => $attributeNames,
                ],
                'values' => [
                    'value' => $attributeValuesExport,
                ],
            ];
        }

        return $attributesExport;
    }

    /**
     * @param Product $product
     * @param int|null $idCombination
     * @return array
     */
    protected function getProductAttributeCombinations(Product $product, int $idCombination = null)
    {
        $attributesExport = [];

        $combinationsData = $product->getAttributeCombinationsById($idCombination, $this->getDefaultLanguage());
        foreach ($combinationsData as $combinationIndex => $combinationData) {
            $attributeNames      = [];
            $attributeValueNames = [];

            if (empty($combinationData['id_attribute_group'] ?? '') || empty($combinationData['id_attribute'] ?? '') || empty($combinationData['group_name'] ?? '') || empty($combinationData['attribute_name'] ?? '')) {
                continue;
            }

            $attributeCode      = 'base_attribute_' . $combinationData['id_attribute_group'];
            $attributeValueCode = 'base_attribute_value_' . $combinationData['id_attribute'];

            foreach ($this->_languages as $languageId => $language) {
                $combinationLanguageData = $product->getAttributeCombinationsById($idCombination, $languageId)[$combinationIndex] ?? [];
                if (empty($combinationLanguageData['id_attribute_group'] ?? '') || empty($combinationLanguageData['id_attribute'] ?? '') || empty($combinationLanguageData['group_name'] ?? '') || empty($combinationLanguageData['attribute_name'] ?? '')) {
                    break;
                }

                $attributeNames[] = [
                    '_attributes' => ['language' => $language['iso_code']],
                    '_cdata'      => $combinationLanguageData['group_name'],
                ];

                $attributeValueNames[] = [
                    '_attributes' => ['language' => $language['iso_code']],
                    '_cdata'      => $combinationLanguageData['attribute_name'],
                ];
            }

            if (count($attributeNames) === 0 || count($attributeValueNames) === 0) {
                continue;
            }

            $attributeValuesExport = [
                'code' => $attributeValueCode,
                'names' => [
                    'name' => $attributeValueNames,
                ],
            ];

            $attributesExport[$attributeCode] = [
                'code' => $attributeCode,
                'names' => [
                    'name' => $attributeNames,
                ],
                'values' => [
                    'value' => $attributeValuesExport,
                ],
            ];
        }

        return $attributesExport;
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductAttributeAvailableForOrder(Product $product)
    {
        $attributeValue = intval($product->available_for_order);

        $attributeValueNames = [];
        foreach ($this->_languages as $languageId => $language)
        {
            $attributeValueNames[] = [
                '_attributes' => ['language' => $language['iso_code']],
                '_cdata'      => $this->_translator->trans($attributeValue === 1 ? 'Yes' : 'No', [], 'Modules.Effectconnectmarketplaces.Admin', $language['locale']),
            ];
        }

        $attributeNames = [];
        foreach ($this->_languages as $languageId => $language)
        {
            $attributeNames[] = [
                '_attributes' => ['language' => $language['iso_code']],
                '_cdata'      => $this->_translator->trans('Available for order', [], 'Modules.Effectconnectmarketplaces.Admin', $language['locale']),
            ];
        }

        $attributeValuesExport = [
            'code'   => $attributeValue,
            'names' => [
                'name' => $attributeValueNames,
            ],
        ];

        $attributesExport['available_for_order'] = [
            'code'   => 'available_for_order',
            'names' => [
                'name' => $attributeNames,
            ],
            'values' => [
                'value' => $attributeValuesExport,
            ],
        ];

        return $attributesExport;
    }
}