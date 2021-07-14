<?php

namespace EffectConnect\Marketplaces\Service\Transformer;

use Address;
use Carrier;
use Cart;
use Combination;
use Configuration;
use Country;
use Currency;
use Customer;
use EffectConnect\Marketplaces\Enums\ExternalFulfilment;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Exception\InitContextFailedException;
use EffectConnect\Marketplaces\Exception\OrderImportFailedException;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use EffectConnect\Marketplaces\Model\TrackingExportQueue;
use EffectConnect\Marketplaces\Service\InitContext;
use EffectConnect\PHPSdk\Core\Model\Filter\HasStatusFilter;
use EffectConnect\PHPSdk\Core\Model\Request\OrderAddress;
use EffectConnect\PHPSdk\Core\Model\Response\BillingAddress as EffectConnectBillingAddress;
use EffectConnect\PHPSdk\Core\Model\Response\Line;
use EffectConnect\PHPSdk\Core\Model\Response\ShippingAddress as EffectConnectShippingAddress;
use Exception;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\PHPSdk\Core\Model\Response\Order as EffectConnectOrder;
use Group;
use Module;
use Order;
use PaymentModule;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use SpecificPrice;
use State;
use Tools;
use Validate;

/**
 * Class OrderImportTransformer
 * @package EffectConnect\Marketplaces\Service\Transformer
 */
class OrderImportTransformer extends AbstractTransformer
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::IMPORT_ORDERS;

    /**
     * Number of decimals to round prices to - PS uses 6 by default.
     */
    const PRICE_DECIMALS = 6;

    /**
     * @var int
     */
    protected $_lastImportedOrderId = 0;

    /**
     * @var string
     */
    protected $_lastImportedOrderReference = '';

    /**
     * OrderImportTransformer constructor.
     * @param InitContext $initContext
     * @param LegacyContext $legacyContext
     * @param CurrencyDataProvider $currencyDataProvider
     * @param LoggerHelper $loggerHelper
     */
    public function __construct(
        InitContext $initContext,
        LegacyContext $legacyContext,
        CurrencyDataProvider $currencyDataProvider,
        LoggerHelper $loggerHelper
    ) {
        $this->_logger = $loggerHelper::createLogger(static::LOGGER_PROCESS);
        parent::__construct($initContext, $legacyContext, $currencyDataProvider);
    }

    /**
     * @param Connection $connection
     * @throws InitContextFailedException
     */
    public function initConnection(Connection $connection)
    {
        $this->init($connection);
    }

    /**
     * @param EffectConnectOrder $ecOrder
     * @return bool
     * @throws OrderImportFailedException
     */
    public function importOrder(EffectConnectOrder $ecOrder)
    {
        // Check if we need to import the order.
        if ($this->skipOrderImport($ecOrder)) {
            return false;
        }

        $customer            = $this->processCustomer($ecOrder);
        $invoiceAddress      = $this->processAddress($ecOrder->getBillingAddress(), $customer);
        $deliveryAddress     = $this->processAddress($ecOrder->getShippingAddress(), $customer);
        $currency            = $this->processCurrency($ecOrder);
        $cart                = $this->processCart($ecOrder, $customer, $invoiceAddress, $deliveryAddress, $currency);
        $cart                = $this->processProducts($ecOrder, $cart);
        $cart                = $this->processCarrier($cart);
        $paymentModule       = $this->processPayment();
        $order               = $this->processOrder($ecOrder, $cart, $paymentModule);
        $this->saveOrderIdentifiers($order, $ecOrder);

        $this->_lastImportedOrderId        = intval($order->id);
        $this->_lastImportedOrderReference = strval($order->reference);

        return true;
    }

    /**
     * @param EffectConnectOrder $order
     * @return bool
     */
    protected function skipOrderImport(EffectConnectOrder $order)
    {
        // Check if order was already imported - identify by EC order number.
        $effectConnectNumber = $order->getIdentifiers()->getEffectConnectNumber();
        $existingOrders      = TrackingExportQueue::getListByEffectConnectNumber($effectConnectNumber);
        if (count($existingOrders) > 0)
        {
            $this->_logger->info('Order ' . $effectConnectNumber . ' skipped because it was already imported.', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $this->getConnection()->id
                ]
            ]);

            return true;
        }

        // Status to fetch orders for depends on connection setting 'order_import_external_fulfilment'.
        // Internal fulfilled orders always have status 'paid'.
        // External fulfilled orders always have status 'completed' AND tag 'external_fulfilment'.
        // To fetch internal as well external orders we should apply the filter 'status paid' or 'status completed and tag external_fulfilment'.
        // When fetching orders we only look at status, so we have to filter the combination of status and tag now.
        $effectConnectOrderIsExternalFulfilled = $this->orderHasExternalFulfilmentTag($order);
        $skipOrderImport                       = false;
        switch ($this->getConnection()->order_import_external_fulfilment)
        {
            case ExternalFulfilment::EXTERNAL_AND_INTERNAL_ORDERS:
                if ($order->getStatus() == HasStatusFilter::STATUS_COMPLETED && !$effectConnectOrderIsExternalFulfilled) {
                    $skipOrderImport = true;
                }
                break;
            case ExternalFulfilment::EXTERNAL_ORDERS:
                if ($order->getStatus() != HasStatusFilter::STATUS_COMPLETED || !$effectConnectOrderIsExternalFulfilled) {
                    $skipOrderImport = true;
                }
                break;
            case ExternalFulfilment::INTERNAL_ORDERS:
            default:
                if ($order->getStatus() != HasStatusFilter::STATUS_PAID || $effectConnectOrderIsExternalFulfilled) {
                    $skipOrderImport = true;
                }
                break;
        }

        if ($skipOrderImport) {
            $this->_logger->info('Order ' . $effectConnectNumber . ' skipped because fulfilment status (is external fulfilled: ' . intval($effectConnectOrderIsExternalFulfilled) . ') does not match connection setting (' . $this->getConnection()->order_import_external_fulfilment . ').', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $this->getConnection()->id
                ]
            ]);

            return true;
        }

        // No reason found for skipping the order import.
        return false;
    }

    /**
     * @param EffectConnectOrder $order
     * @return Customer
     * @throws OrderImportFailedException
     */
    protected function processCustomer(EffectConnectOrder $order)
    {
        // Load the customer by email address.
        $emailAddress = $order->getBillingAddress()->getEmail();
        $customerId = intval(Customer::customerExists($emailAddress, true));
        if ($customerId > 0)
        {
            try {
                $customer = new Customer($customerId);
            } catch (Exception $e) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Fetch customer - ' . $e->getMessage());
            }
        }
        else
        {
            // Create new customer.
            $customer            = new Customer();
            $customer->email     = $emailAddress;
            $customer->firstname = $this->convertCustomerName($order->getBillingAddress()->getFirstName());
            $customer->lastname  = $this->convertCustomerName($order->getBillingAddress()->getLastName());
            $customer->passwd    = Tools::passwdGen(8, 'RANDOM');
            $customer->id_gender = $this->getGender($order);

            // Try to create the customer.
            try {
                $success = $customer->add();
            } catch (Exception $e) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Create customer - ' . $e->getMessage());
            }

            if ($success === false) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Create customer - unknown reason');
            }
        }

        // Assign the customer to the customer group that was set in the order import connection settings.
        try {
            $this->assignCustomerGroup($customer);
        } catch (Exception $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create customer group - ' . $e->getMessage());
        }

        return $customer;
    }

    /**
     * @param EffectConnectBillingAddress|EffectConnectShippingAddress $effectConnectAddress
     * @param Customer $customer
     * @return Address
     * @throws OrderImportFailedException
     */
    protected function processAddress($effectConnectAddress, Customer $customer)
    {
        $address              = new Address();
        $address->id_customer = $customer->id;
        $address->alias       = ($effectConnectAddress instanceof EffectConnectBillingAddress ? 'billing_' : 'shipping_') . time();
        $address->firstname   = $effectConnectAddress->getFirstName();
        $address->lastname    = $effectConnectAddress->getLastName();
        $address->company     = $effectConnectAddress->getCompany();
        $address->postcode    = $effectConnectAddress->getZipCode();
        $address->phone       = $effectConnectAddress->getPhone();
        $address->vat_number  = $effectConnectAddress->getTaxNumber();
        $address->city        = $effectConnectAddress->getCity();
        $address->id_state    = intval(State::getIdByName($effectConnectAddress->getState()));

        // Process country.
        if (!Validate::isLanguageIsoCode($effectConnectAddress->getCountry())) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create address - could not process country ISO code ' . $effectConnectAddress->getCountry());
        }
        $address->id_country = Country::getByIso($effectConnectAddress->getCountry());

        // Process address.
        $address->address1 = implode(' ', array_filter(
                [
                    $effectConnectAddress->getStreet(),
                    $effectConnectAddress->getHouseNumber(),
                    $effectConnectAddress->getHouseNumberExtension(),
                ]
            )
        );
        if (!empty($effectConnectAddress->getAddressNote())) {
            $address->address2 = $effectConnectAddress->getAddressNote();
        }

        // Try to create the address.
        try {
            $success = $address->add();
        } catch (PrestaShopDatabaseException $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create address - ' . $e->getMessage());
        } catch (PrestaShopException $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create address - ' . $e->getMessage());
        }

        if ($success === false) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create address - unknown reason');
        }

        return $address;
    }

    /**
     * @param EffectConnectOrder $order
     * @return Currency
     * @throws OrderImportFailedException
     */
    protected function processCurrency(EffectConnectOrder $order)
    {
        $currency = $this->_currencyDataProvider->getCurrencyByIsoCode($order->getCurrency());
        if (is_null($currency)) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Shop currency [' . $order->getCurrency() . '] not found');
        }

        // TODO: unfortunately in PS 1.7.6.5 it's still necessary to use the context.
        //   See Product::getPriceStatic() - this function uses the context to fetch the currency.
        //   If we don't set the context here, then our 'Specific Prices' won't work because the wrong currency could be used.
        $this->_initContext->setCurrency($currency);

        return $currency;
    }

    /**
     * @param EffectConnectOrder $order
     * @param Customer $customer
     * @param Address $invoiceAddress
     * @param Address $deliveryAddress
     * @param Currency $currency
     * @return Cart
     * @throws OrderImportFailedException
     */
    protected function processCart(EffectConnectOrder $order, Customer $customer, Address $invoiceAddress, Address $deliveryAddress, Currency $currency)
    {
        $cart = new Cart();
        $cart->id_customer         = $customer->id;
        $cart->id_address_delivery = $deliveryAddress->id;
        $cart->id_address_invoice  = $invoiceAddress->id;
        $cart->id_lang             = $this->getDefaultLanguage();
        $cart->id_shop             = $this->getShopId();
        $cart->id_currency         = intval($currency->id);
        $cart->secure_key          = $customer->secure_key;

        try {
            $success = $cart->add();
        } catch (PrestaShopDatabaseException $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create cart - ' . $e->getMessage());
        } catch (PrestaShopException $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create cart - ' . $e->getMessage());
        }

        if ($success === false) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create cart - unknown reason');
        }

        if (!method_exists($cart, 'setEffectConnectOrder')) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create cart - function setEffectConnectOrder was not found');
        }
        $cart::setEffectConnectOrder($order);

        return $cart;
    }

    /**
     * @param EffectConnectOrder $order
     * @param Cart $cart
     * @return Cart
     * @throws OrderImportFailedException
     */
    protected function processProducts(EffectConnectOrder $order, Cart $cart)
    {
        // Support for duplicate products with different prices is unsupported by Prestashop, so we recalculate these prices to average price.
        $recalculatedPricesByProductId          = $this->recalculateDuplicateProductsWithDifferentPrices($order->getLines());
        $recalculatedPricesByProductIdProcessed = [];

        foreach ($order->getLines() as $orderLine)
        {
            // Try to match the product by its Prestashop ID (which we provided before in the catalog export to EffectConnect).
            $productIdentifier = $orderLine->getProduct()->getIdentifier();
            if (empty($productIdentifier)) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Process product - product identifier empty');
            }
            $productId         = $this->getProductIdFromProductIdentifier($productIdentifier);
            $combinationId     = $this->getCombinationIdFromProductIdentifier($productIdentifier);

            // Try to load the product by its id.
            try {
                $product = new Product($productId, true);
            } catch (PrestaShopDatabaseException $e) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Process product ' . $productIdentifier . ' - ' . $e->getMessage());
            } catch (PrestaShopException $e) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Process product ' . $productIdentifier . ' - ' . $e->getMessage());
            }

            // Try to load the product and combination by its id.
            if ($combinationId !== null) {
                try {
                    new Combination($combinationId);
                } catch (PrestaShopDatabaseException $e) {
                    throw new OrderImportFailedException($this->getConnection()->id, 'Process product (combination) ' . $productIdentifier . ' - ' . $e->getMessage());
                } catch (PrestaShopException $e) {
                    throw new OrderImportFailedException($this->getConnection()->id, 'Process product (combination) ' . $productIdentifier . ' - ' . $e->getMessage());
                }
            }

            // NOTE: for each product we can only set 1 price in the cart, hence we use the $recalculatedPricesByProductId.
            $effectConnectProductId = $orderLine->getProductId();
            if (!isset($recalculatedPricesByProductId[$effectConnectProductId])) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Add product ' . $productIdentifier . ' to cart failed when fetching calculated price');
            }
            $orderLineAmountIncludingTax = $recalculatedPricesByProductId[$effectConnectProductId];

            // Convert product price (always includes tax) to price without tax.
            // TODO: can the tax rate depend on the customer group and how to handle this?
            $orderLineAmountExcludingTax = $orderLineAmountIncludingTax / (100 + $product->tax_rate) * 100;

            // Only add specific price if EC price differs from Presta price (and if we did already set one for current product).
            $orderLineAmountExcludingTaxRounded = Tools::ps_round($orderLineAmountExcludingTax, self::PRICE_DECIMALS);
            $prestashopProductPrice             = $product->getPrice(false, $combinationId, self::PRICE_DECIMALS);
            if (
                $orderLineAmountExcludingTaxRounded != $prestashopProductPrice
                && !in_array($effectConnectProductId, $recalculatedPricesByProductIdProcessed)
            )
            {
                // Set specific price for current product.
                $specificPrice = new SpecificPrice();
                $specificPrice->id_product           = $productId;
                $specificPrice->id_product_attribute = $combinationId;
                $specificPrice->id_cart              = $cart->id;
                $specificPrice->id_shop              = $cart->id_shop;
                $specificPrice->id_currency          = $cart->id_currency;
                $specificPrice->id_customer          = $cart->id_customer;
                $specificPrice->id_country           = 0; // TODO
                $specificPrice->id_group             = 0; // TODO
                $specificPrice->from_quantity        = 1;
                $specificPrice->price                = $orderLineAmountExcludingTaxRounded;
                $specificPrice->reduction_type       = 'amount';
                $specificPrice->reduction_tax        = false;
                $specificPrice->reduction            = 0;
                $specificPrice->from                 = "0000-00-00 00:00:00"; // TODO
                $specificPrice->to                   = "0000-00-00 00:00:00"; // TODO
                try {
                    $specificPrice->add();
                    $recalculatedPricesByProductIdProcessed[] = $effectConnectProductId;
                } catch (PrestaShopDatabaseException $e) {
                    throw new OrderImportFailedException($this->getConnection()->id, 'Add specific price for product ' . $productIdentifier);
                } catch (PrestaShopException $e) {
                    throw new OrderImportFailedException($this->getConnection()->id, 'Add specific price for product ' . $productIdentifier);
                }
            }

            // Add the product to the cart.
            $success = $cart->updateQty(1, $productId, $combinationId, false, 'up', 0, null, false);

            if ($success !== true) {
                throw new OrderImportFailedException($this->getConnection()->id, 'Add product ' . $productIdentifier . ' to cart returned ' . intval($success));
            }
        }

        return $cart;
    }

    /**
     * @param Cart $cart
     * @return Cart
     * @throws OrderImportFailedException
     */
    protected function processCarrier(Cart $cart)
    {
        // Process carrier.
        $carrier = new Carrier($this->getConnection()->order_import_id_carrier);
        if (intval($carrier->id) === 0) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create cart - carrier ' . $this->getConnection()->order_import_id_carrier . ' not found');
        }

        // Set delivery option to selected carrier.
        $deliveryOption = [$cart->id_address_delivery => strval($carrier->id) . ","];
        $cart->setDeliveryOption($deliveryOption);

        // By fetching the delivery option after setting it, we can verify if it was a valid carrier for current cart.
        if (false === $cart->getDeliveryOption(null, true)) {
            // TODO: select other carrier to make sure the order is imported anyway?
            throw new OrderImportFailedException($this->getConnection()->id, 'Create cart - carrier ' . $this->getConnection()->order_import_id_carrier . ' is invalid');
        }

        // Save the updated cart.
        try {
            $result = $cart->update();
        } catch (PrestaShopDatabaseException $e) {
            $result = false;
        } catch (PrestaShopException $e) {
            $result = false;
        }
        if (false === $result) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Saving cart failed when added carrier ' . $this->getConnection()->order_import_id_carrier);
        }

        return $cart;
    }

    /**
     * @return PaymentModule
     * @throws OrderImportFailedException
     */
    protected function processPayment()
    {
        /** @var PaymentModule $paymentModule */
        $paymentModule = Module::getInstanceById($this->getConnection()->order_import_id_payment_module);
        if ($paymentModule === false || !($paymentModule instanceof PaymentModule)) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Payment module with ID ' . $this->getConnection()->order_import_id_payment_module . ' failed to init');
        }

        return $paymentModule;
    }

    /**
     * @param EffectConnectOrder $order
     * @param Cart $cart
     * @param PaymentModule $paymentModule
     * @return Order
     * @throws OrderImportFailedException
     */
    protected function processOrder(EffectConnectOrder $order, Cart $cart, PaymentModule $paymentModule)
    {
        try {
            // This will actually save the order to database - we suppress warnings that externally plugins may throw (for example blockgiftlistpro).
            $result = @$paymentModule->validateOrder(
                intval($cart->id),
                $this->getOrderState($order),
                $cart->getOrderTotal(true, Cart::BOTH), // We use (true, Cart::BOTH), because this is the same way this is done in PaymentModule::validateOrder()
                $paymentModule->displayName,
                $this->getOrderComment($order),
                ['ec_send_mail' => boolval($this->getConnection()->order_import_send_emails)],
                null,
                false,
                $cart->secure_key
            );
        } catch (Exception $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create order - ' . $e->getMessage());
        }

        if ($result === false) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create order');
        }

        try {
            $order = new Order($paymentModule->currentOrder);
        } catch (PrestaShopDatabaseException $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create order - ' . $e->getMessage());
        } catch (PrestaShopException $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Create order - ' . $e->getMessage());
        }

        return $order;
    }

    /**
     * @param EffectConnectOrder $order
     * @return string
     */
    protected function getOrderComment(EffectConnectOrder $order)
    {
        // TODO: translate
        $identifiers = $order->getIdentifiers();
        $channelInfo = $order->getChannelInfo();
        $orderComment = [
            ('Order imported from EffectConnect Marketplaces'),
            ('Channel type:') . ' ' . $channelInfo->getType(),
            ('Channel title:') . ' ' . $channelInfo->getTitle(),
            ('Order number channel:') . ' ' . $identifiers->getChannelNumber()
        ];

        return implode("\n", $orderComment);
    }

    /**
     * At the moment we import orders with status 'paid' and 'completed' (shipped).
     *
     * @param EffectConnectOrder $order
     * @return int
     */
    protected function getOrderState(EffectConnectOrder $order)
    {
        if ($order->getStatus() == HasStatusFilter::STATUS_COMPLETED) {
            return intval(Configuration::get('PS_OS_SHIPPING'));
        }
        return intval(Configuration::get('PS_OS_PAYMENT'));
    }

    /**
     * @param EffectConnectOrder $order
     * @return int
     */
    protected function getGender(EffectConnectOrder $order)
    {
        if ($order->getBillingAddress()->getSalutation() == OrderAddress::SALUTATION_MALE) {
            $gender = 1;
        } elseif ($order->getBillingAddress()->getSalutation() == OrderAddress::SALUTATION_FEMALE) {
            $gender = 2;
        } else {
            $gender = 0;
        }
        return $gender;
    }

    /**
     * Number of products in EC order line is always 1 - this can be a problem when a product was ordered multiple times.
     * We get in trouble when the prices for these products differ!
     * Example: SKU 001 was ordered once with price 1,- and ordered once with price 2,-.
     * In Prestashop we can only have ONE SpecificPrice for each product ID in a cart.
     * To solve this we recalculate the product price per piece to 1,50 - the totals will be correct then.
     *
     * @param Line[] $orderLines
     * @return array
     */
    protected function recalculateDuplicateProductsWithDifferentPrices(array $orderLines)
    {
        $recalculatedPricesByProductId = [];

        // First group all different prices by product ID.
        $pricesByProductId = [];
        foreach ($orderLines as $orderLine) {
            $productId                       = $orderLine->getProductId();
            $price                           = $orderLine->getLineAmount();
            $pricesByProductId[$productId][] = $price;
        }

        // Now for each order line, check if we need to do a recalculation.
        foreach ($orderLines as $orderLine) {
            $productId                                 = $orderLine->getProductId();
            $recalculatedPricesByProductId[$productId] = array_sum($pricesByProductId[$productId]) / count($pricesByProductId[$productId]);;
        }

        return $recalculatedPricesByProductId;
    }

    /**
     * @param Order $order
     * @param EffectConnectOrder $ecOrder
     * @throws OrderImportFailedException
     */
    protected function saveOrderIdentifiers(Order $order, EffectConnectOrder $ecOrder)
    {
        // TODO: use repository for save/get functionality?

        $orderLineIds = [];
        foreach ($ecOrder->getLines() as $orderLine) {
            $orderLineIds[] = $orderLine->getIdentifiers()->getEffectConnectId();
        }

        $record                                        = new TrackingExportQueue();
        $record->id_order                              = $order->id;
        $record->id_connection                         = $this->getConnection()->id;
        $record->ec_marketplaces_identification_number = $ecOrder->getIdentifiers()->getEffectConnectNumber();
        $record->ec_marketplaces_channel_number        = $ecOrder->getIdentifiers()->getChannelNumber();
        $record->order_imported_at                     = date('Y-m-d H:i:s');
        $record->setFormattedEcMarketplacesOrderLineIds($orderLineIds);
        try {
            $result = $record->save(true);
        } catch (PrestaShopException $e) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Save order identifier - ' . $e->getMessage());
        }

        if (!$result) {
            throw new OrderImportFailedException($this->getConnection()->id, 'Save order identifier');
        }
    }

    /**
     * @param EffectConnectOrder $order
     * @return bool
     */
    protected function orderHasExternalFulfilmentTag(EffectConnectOrder $order)
    {
        $orderHasExternalFulfilmentTag = false;
        foreach ($order->getTags() as $orderTag)
        {
            if (strval($orderTag->getTag()) === 'external_fulfilment')
            {
                $orderHasExternalFulfilmentTag = true;
                break;
            }
        }
        return $orderHasExternalFulfilmentTag;
    }

    /**
     * @param Customer $customer
     * @throws Exception
     * @return bool
     */
    protected function assignCustomerGroup(Customer $customer)
    {
        if (!Group::isFeatureActive()) {
            return false;
        }

        $customerGroupId = intval($this->getConnection()->order_import_id_group);
        $groups = $customer->getGroups();
        if (!in_array($customerGroupId, $groups)) {
            $groups[] = $customerGroupId;
            $customer->updateGroup($groups);
        }

        // Reset customer group cache, otherwise later on when adding a carrier, we don't get our added group.
        Customer::resetAddressCache();

        return true;
    }

    /**
     * @return int
     */
    public function getLastImportedOrderId()
    {
        return $this->_lastImportedOrderId;
    }

    /**
     * @return string
     */
    public function getLastImportedOrderReference()
    {
        return $this->_lastImportedOrderReference;
    }

    /**
     * For customer names, only letters and the dot (.) character, followed by a space, are allowed.
     * Let's replace a dot following by a non-space character by a dot following by a space character.
     *
     * @param string $name
     * @return string
     */
    public function convertCustomerName(string $name) {
        return preg_replace_callback('~\.([^ ])~', function ($matches) {
            return '. ' . $matches[1];
        }, $name);
    }
}