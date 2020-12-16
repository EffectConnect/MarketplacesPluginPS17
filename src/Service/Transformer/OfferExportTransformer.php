<?php

namespace EffectConnect\Marketplaces\Service\Transformer;

use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Exception\FileCreationFailedException;
use EffectConnect\Marketplaces\Exception\InitContextFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use Product;

/**
 * Class OfferExportTransformer
 * @package EffectConnect\Marketplaces\Service\Transformer
 */
class OfferExportTransformer extends CatalogExportTransformer
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::EXPORT_OFFERS;

    /**
     * The directory where the catalog XML needs to be generated.
     */
    protected const CONTENT_TYPE = 'offer';

    /**
     * @param Connection $connection
     * @param array $productIdsToExport
     * @return false|string
     * @throws FileCreationFailedException
     * @throws InitContextFailedException
     */
    public function buildOfferXml(Connection $connection, array $productIdsToExport = [])
    {
        return $this->buildCatalogXml($connection, $productIdsToExport);
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

        return [$productOptionExport];
    }

    /**
     * @param Product $product
     * @return array
     */
    protected function getProductOptionsArrayFromCombinationProduct(Product $product)
    {
        $productOptionsExport = [];

        $combinationsArray = $product->getAttributesResume($this->getDefaultLanguage());
        foreach ($combinationsArray as $combinationArray)
        {
            $idCombination       = intval($combinationArray['id_product_attribute']);
            $identifier          = $this->getProductIdentifier(intval($product->id), $idCombination);
            $productOptionExport = $this->getProductOptionArray($identifier, strval($combinationArray['reference']), strval($combinationArray['ean13']), floatval($combinationArray['wholesale_price']), $product, $idCombination);

            if (empty($productOptionExport)) {
                continue;
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
        $productOptionExport = [
            'identifier' => $identifier,
            'stock'      => $this->getStock($product, $idCombination),
            'cost'       => number_format($this->convertPriceToEuro($wholeSalePrice), 2),
        ];

        // Product prices
        $price                        = $this->getPrice($product, $idCombination);
        $priceOriginal                = $this->getPriceOriginal($product, $idCombination);
        $productOptionExport['price'] = number_format($price, 2);
        if ($priceOriginal !== null && $priceOriginal > $price) {
            $productOptionExport['priceOriginal'] = number_format($priceOriginal, 2);
        }

        // Delivery time
        $deliveryTime = $this->getDeliveryTime($product);
        if (!empty($deliveryTime))
        {
            $productOptionExport['deliveryTime'] = $deliveryTime;
        }

        return $productOptionExport;
    }
}