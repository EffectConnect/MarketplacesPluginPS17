<?php

namespace EffectConnect\Marketplaces\Service\Transformer;

use Currency;
use EffectConnect\Marketplaces\Exception\InitContextFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\InitContext;
use Monolog\Logger;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;

/**
 * Class AbstractTransformer
 * @package EffectConnect\Marketplaces\Service\Transformer
 */
class AbstractTransformer
{
    /**
     * @var InitContext
     */
    protected $_initContext;

    /**
     * @var LegacyContext
     */
    protected $_legacyContext; // TODO: dont need this when we have InitContext

    /**
     * @var Connection
     */
    protected $_connection;

    /**
     * Contains an array of active languages for loaded shop with the language id as key and the language iso code as value.
     * @var array
     */
    protected $_languageIsoCodeById = [];

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var CurrencyDataProvider
     */
    protected $_currencyDataProvider;

    /**
     * @var Currency|null
     */
    protected $_currencyEuro;

    /**
     * @var Currency
     */
    protected $_currencyShop;

    /**
     * AbstractTransformer constructor.
     * @param InitContext $initContext
     * @param LegacyContext $legacyContext
     * @param CurrencyDataProvider $currencyDataProvider
     */
    public function __construct(
        InitContext $initContext,
        LegacyContext $legacyContext,
        CurrencyDataProvider $currencyDataProvider
    ) {
        $this->_initContext          = $initContext;
        $this->_legacyContext        = $legacyContext;
        $this->_currencyDataProvider = $currencyDataProvider;
    }

    /**
     * @param Connection $connection
     * @throws InitContextFailedException
     */
    protected function init(Connection $connection)
    {
        $this->_connection = $connection;

        // Init context.
        $this->_initContext->initContext($connection);
        $this->_initContext->setShop($this->_connection->id_shop); // TODO: unfortunately in PS 1.7.6.5 this is still necessary

        // Load language data.
        $this->loadLanguageData();

        // Load currency data.
        $this->loadCurrencyData();
    }

    /**
     * Load currency data.
     */
    protected function loadCurrencyData()
    {
        $this->_currencyEuro = $this->_currencyDataProvider->getCurrencyByIsoCode('EUR');
        $this->_currencyShop = $this->_currencyDataProvider->getDefaultCurrency();

        if (is_null($this->_currencyEuro))
        {
            $this->_logger->info('Shop has no EUR currency, product prices will not be converted.', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $this->getConnection()->id
                ]
            ]);
        }
        elseif ($this->_currencyEuro->id != $this->_currencyShop->id)
        {
            $this->_logger->info('Shop prices are not in EUR and will be converted.', [
                'process'         => static::LOGGER_PROCESS,
                'conversion_data' => [
                    'currency' => $this->_currencyShop->iso_code,
                    'rate'     => $this->_currencyEuro->conversion_rate,
                ],
                'connection'      => [
                    'id' => $this->getConnection()->id
                ]
            ]);
        }
    }

    /**
     * Load all shop languages.
     */
    protected function loadLanguageData()
    {
        $languages = $this->_legacyContext->getContext()->language::getLanguages(true, $this->getConnection()->id_shop);
        foreach ($languages as $language) {
            $idLang = intval($language['id_lang']);
            $this->_languageIsoCodeById[$idLang] = $language['iso_code'];
        }

        $this->_logger->info('Language data loaded.', [
            'process'       => static::LOGGER_PROCESS,
            'connection'    => [
                'id' => $this->getConnection()->id
            ],
            'language_data' => $this->_languageIsoCodeById,
            'shop'          => $this->getConnection()->id_shop
        ]);
    }

    /**
     * @return int
     */
    protected function getDefaultLanguage()
    {
        if (count($this->_languageIsoCodeById) > 0) {
            return array_key_first($this->_languageIsoCodeById);
        }
        return 0;
    }

    /**
     * @return int
     */
    protected function getShopId()
    {
        return $this->_connection->id_shop;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @param int $idProduct
     * @param int|null $idCombination
     * @return string
     */
    protected function getProductIdentifier(int $idProduct, int $idCombination = null)
    {
        $identifier = strval($idProduct);
        if ($idCombination !== null) {
            $identifier .= '-' . strval($idCombination);
        }
        return $identifier;
    }

    /**
     * @param string $productIdentifier
     * @return int|null
     */
    protected function getProductIdFromProductIdentifier(string $productIdentifier)
    {
        $productIdentifierParts = explode('-', $productIdentifier);
        if (is_array($productIdentifierParts) && isset($productIdentifierParts[0])) {
            return intval($productIdentifierParts[0]);
        }
        return null;
    }

    /**
     * @param string $productIdentifier
     * @return int|null
     */
    protected function getCombinationIdFromProductIdentifier(string $productIdentifier)
    {
        $productIdentifierParts = explode('-', $productIdentifier);
        if (is_array($productIdentifierParts) && isset($productIdentifierParts[1])) {
            return intval($productIdentifierParts[1]);
        }
        return null;
    }
}