<?php

namespace EffectConnect\Marketplaces\Service\Api;

use EffectConnect\Marketplaces\Enums\ExternalFulfilment;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use EffectConnect\Marketplaces\Exception\ApiCallFailedException;
use EffectConnect\Marketplaces\Exception\OrderImportFailedException;
use EffectConnect\Marketplaces\Exception\SdkCoreNotInitializedException;
use EffectConnect\Marketplaces\Helper\LoggerHelper;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Transformer\OrderImportTransformer;
use EffectConnect\PHPSdk\Core\Exception\InvalidPropertyValueException;
use EffectConnect\PHPSdk\Core\Exception\MissingFilterValueException;
use EffectConnect\PHPSdk\Core\Model\Filter\HasStatusFilter;
use EffectConnect\PHPSdk\Core\Model\Filter\HasTagFilter;
use EffectConnect\PHPSdk\Core\Model\Filter\TagFilterValue;
use EffectConnect\PHPSdk\Core\Model\Request\OrderList;
use EffectConnect\PHPSdk\Core\Model\Request\OrderUpdate;
use EffectConnect\PHPSdk\Core\Model\Request\OrderUpdateRequest;
use EffectConnect\PHPSdk\Core\Model\Response\Order as EffectConnectOrder;
use Exception;

/**
 * Class OrderImportApi
 * @package EffectConnect\Marketplaces\Service\Api
 */
class OrderImportApi extends AbstractApi
{
    /**
     * The logger process for this service.
     */
    protected const LOGGER_PROCESS = LoggerProcess::IMPORT_ORDERS;

    /**
     * Order import failed tag.
     */
    protected const ORDER_IMPORT_FAILED_TAG = 'order_import_failed';

    /**
     * Order import succeeded tag.
     */
    protected const ORDER_IMPORT_SUCCEEDED_TAG = 'order_import_succeeded';

    /**
     * Order import skipped tag.
     */
    protected const ORDER_IMPORT_SKIPPED_TAG = 'order_import_skipped';

    /**
     * Exclude tag filters.
     */
    protected const EXCLUDE_TAG_FILTERS = [
        self::ORDER_IMPORT_FAILED_TAG,
        self::ORDER_IMPORT_SUCCEEDED_TAG,
        self::ORDER_IMPORT_SKIPPED_TAG
    ];

    /**
     * @var OrderImportTransformer
     */
    protected $_orderImportTransformer;

    /**
     * OrderImportApi constructor.
     * @param LoggerHelper $loggerHelper
     * @param OrderImportTransformer $orderImportTransformer
     */
    public function __construct(
        LoggerHelper $loggerHelper,
        OrderImportTransformer $orderImportTransformer
    ) {
        $this->_orderImportTransformer = $orderImportTransformer;
        $this->_logger                 = $loggerHelper::createLogger(static::LOGGER_PROCESS);
    }

    /**
     * @param Connection $connection
     * @throws OrderImportFailedException
     */
    public function importOrders(Connection $connection)
    {
        $this->logStart($connection);

        if (false === boolval($connection->order_import_active))
        {
            $this->_logger->warning('Order import skipped because process is disabled for current connection.', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            return;
        }

        if (false === boolval($connection->is_active))
        {
            $this->_logger->warning('Order import skipped because connection is not active.', [
                'process'    => static::LOGGER_PROCESS,
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            return;
        }

        try {
            $this->initializeSdkByConnection($connection);
        } catch (Exception $e) {
            $this->_logger->error('Order import failed when initializing SDK.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new OrderImportFailedException($connection->id, 'Order import failed when initializing SDK - ' . $e->getMessage());
        }

        try {
            $ecOrders = $this->orderListReadCall($connection);
        } catch (Exception $e) {
            $this->_logger->error('Order import failed when fetching orders.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new OrderImportFailedException($connection->id, 'Order import failed when fetching orders - ' . $e->getMessage());
        }

        try {
            $this->_orderImportTransformer->initConnection($connection);
        } catch (Exception $e) {
            $this->_logger->error('Order import failed when initializing context.', [
                'process'    => static::LOGGER_PROCESS,
                'message'    => $e->getMessage(),
                'connection' => [
                    'id' => $connection->id
                ]
            ]);
            $this->logEnd($connection);
            throw new OrderImportFailedException($connection->id, 'Order import failed when initializing context - ' . $e->getMessage());
        }

        $this->_logger->info(count($ecOrders) . ' order(s) to import.', [
            'process'    => static::LOGGER_PROCESS,
            'connection' => [
                'id' => $connection->id
            ]
        ]);

        foreach ($ecOrders as $ecOrder)
        {
            try
            {
                $result = $this->_orderImportTransformer->importOrder($ecOrder);
                if ($result)
                {
                    $this->_logger->info('Order imported successfully.', [
                        'process'       => static::LOGGER_PROCESS,
                        'connection'    => [
                            'id' => $connection->id
                        ],
                        'order'         => [
                            'effect_connect_number' => $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                            'shop_number'           => $this->_orderImportTransformer->getLastImportedOrderReference()
                        ]
                    ]);

                    // Update EC order with shop order number and ID.
                    try {
                        $this->orderUpdateCall(
                            $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                            $this->_orderImportTransformer->getLastImportedOrderId(),
                            $this->_orderImportTransformer->getLastImportedOrderReference()
                        );
                    } catch (Exception $e) {
                        $this->_logger->error('Order update call (update identifiers) to EffectConnect failed.', [
                            'process'    => static::LOGGER_PROCESS,
                            'message'    => $e->getMessage(),
                            'connection' => [
                                'id' => $connection->id
                            ],
                            'order'      => [
                                'effect_connect_number' => $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                                'shop_number'           => $this->_orderImportTransformer->getLastImportedOrderReference()
                            ]
                        ]);
                        continue;
                    }

                    // Send feedback to EffectConnect that we have successfully imported the order.
                    try
                    {
                        $this->orderUpdateAddTagCall(
                            $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                            self::ORDER_IMPORT_SUCCEEDED_TAG
                        );
                    }
                    catch (Exception $e)
                    {
                        $this->_logger->error('Order update call (add success tag) to EffectConnect failed.', [
                            'process'    => static::LOGGER_PROCESS,
                            'message'    => $e->getMessage(),
                            'connection' => [
                                'id' => $connection->id
                            ],
                            'order'      => [
                                'effect_connect_number' => $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                                'shop_number'           => $this->_orderImportTransformer->getLastImportedOrderReference()
                            ]
                        ]);
                    }
                }
                else
                {
                    // Order was skipped for a reason (which was already logged by the OrderTransformer).
                    // We'll send a 'skipped' tag for these orders, to make sure we won't import these orders again.
                    try
                    {
                        $this->orderUpdateAddTagCall(
                            $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                            self::ORDER_IMPORT_SKIPPED_TAG
                        );
                    }
                    catch (Exception $e)
                    {
                        $this->_logger->error('Order update call (add skipped tag) to EffectConnect failed.', [
                            'process'    => static::LOGGER_PROCESS,
                            'message'    => $e->getMessage(),
                            'connection' => [
                                'id' => $connection->id
                            ],
                            'order'      => [
                                'effect_connect_number' => $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                            ]
                        ]);
                    }
                }
            }
            catch (Exception $e)
            {
                $this->_logger->error('Order import failed.', [
                    'process'    => static::LOGGER_PROCESS,
                    'message'    => $e->getMessage(),
                    'file'       => $e->getFile(),
                    'line'       => $e->getLine(),
                    'connection' => [
                        'id' => $connection->id
                    ],
                    'order'      => [
                        'effect_connect_number' => $ecOrder->getIdentifiers()->getEffectConnectNumber()
                    ]
                ]);

                // Send feedback to EffectConnect that we have failed to import the order.
                try
                {
                    $this->orderUpdateAddTagCall(
                        $ecOrder->getIdentifiers()->getEffectConnectNumber(),
                        self::ORDER_IMPORT_FAILED_TAG
                    );
                }
                catch (Exception $e)
                {
                    $this->_logger->error('Order update call (add fail tag) to EffectConnect failed.', [
                        'process'    => static::LOGGER_PROCESS,
                        'message'    => $e->getMessage(),
                        'connection' => [
                            'id' => $connection->id
                        ],
                        'order'      => [
                            'effect_connect_number' => $ecOrder->getIdentifiers()->getEffectConnectNumber()
                        ]
                    ]);
                }
            }
        }

        $this->logEnd($connection);
    }

    /**
     * @param string $effectConnectNumber
     * @param string $tag
     * @throws ApiCallFailedException
     * @throws InvalidPropertyValueException
     * @throws SdkCoreNotInitializedException
     */
    protected function orderUpdateAddTagCall(string $effectConnectNumber, string $tag)
    {
        $orderCall = $this->getSdkCore()->OrderCall();

        $orderData = new OrderUpdate();
        $orderData
            ->setOrderIdentifierType(OrderUpdate::TYPE_EFFECTCONNECT_NUMBER)
            ->setOrderIdentifier($effectConnectNumber)
            ->addTag($tag);

        $orderUpdate = new OrderUpdateRequest();
        $orderUpdate->addOrderUpdate($orderData);

        $apiCall = $orderCall->update($orderUpdate);
        $this->callAndResolveResponse($apiCall);
    }

    /**
     * @param string $effectConnectNumber
     * @param int $shopOrderId
     * @param string $shopOrderNumber
     * @throws ApiCallFailedException
     * @throws InvalidPropertyValueException
     * @throws SdkCoreNotInitializedException
     */
    protected function orderUpdateCall(string $effectConnectNumber, int $shopOrderId, string $shopOrderNumber)
    {
        $orderCall = $this->getSdkCore()->OrderCall();

        $orderData = new OrderUpdate();
        $orderData
            ->setOrderIdentifierType(OrderUpdate::TYPE_EFFECTCONNECT_NUMBER)
            ->setOrderIdentifier($effectConnectNumber)
            ->setConnectionIdentifier($shopOrderId)
            ->setConnectionNumber($shopOrderNumber);

        $orderUpdate = new OrderUpdateRequest();
        $orderUpdate->addOrderUpdate($orderData);

        $apiCall = $orderCall->update($orderUpdate);
        $this->callAndResolveResponse($apiCall);
    }

    /**
     * @param Connection $connection
     * @return EffectConnectOrder[]
     * @throws ApiCallFailedException
     * @throws InvalidPropertyValueException
     * @throws MissingFilterValueException
     * @throws SdkCoreNotInitializedException
     */
    protected function orderListReadCall(Connection $connection)
    {
        $orderListCall  = $this->getSdkCore()->OrderListCall();
        $orderList      = new OrderList();
        $this->addStatusFilters($orderList, $connection);
        $this->addExcludeTagFilters($orderList);
        $apiCall = $orderListCall->read($orderList);

        $timeOut = 0;
        if (intval($connection->order_import_api_call_timeout) > 0) {
            $timeOut = intval($connection->order_import_api_call_timeout);
        }
        $result = $this->callAndResolveResponse($apiCall, $timeOut);
        return $result->getOrders();
    }

    /**
     * Status to fetch orders for depends on connection setting 'order_import_external_fulfilment'.
     * Internal fulfilled orders always have status 'paid'.
     * External fulfilled orders always have status 'completed' AND tag 'external_fulfilment'.
     * To fetch internal as well external orders we should apply the filter 'status paid' or 'status completed and tag external_fulfilment'.
     * The latter is not possible in current API version, so let's only take status into account and filter by tag afterwards in OrderImportTransformer.
     *
     * @param OrderList $orderList
     * @param Connection $connection
     * @throws InvalidPropertyValueException
     * @throws MissingFilterValueException
     */
    protected function addStatusFilters(OrderList &$orderList, Connection $connection)
    {
        $hasStatusFilter = new HasStatusFilter();

        switch ($connection->order_import_external_fulfilment)
        {
            case ExternalFulfilment::EXTERNAL_AND_INTERNAL_ORDERS:
                $statusFilters = [
                    HasStatusFilter::STATUS_PAID,
                    HasStatusFilter::STATUS_COMPLETED,
                ];
                break;
            case ExternalFulfilment::EXTERNAL_ORDERS:
                $statusFilters = [HasStatusFilter::STATUS_COMPLETED];
                break;
            case ExternalFulfilment::INTERNAL_ORDERS:
            default:
                $statusFilters = [HasStatusFilter::STATUS_PAID];
                break;
        }

        $hasStatusFilter->setFilterValue($statusFilters);
        $orderList->addFilter($hasStatusFilter);
    }

    /**
     * Add exclude tag filters.
     *
     * @param OrderList $orderList
     * @throws MissingFilterValueException
     * @throws InvalidPropertyValueException
     */
    protected function addExcludeTagFilters(OrderList &$orderList)
    {
        $hasTagFilter = new HasTagFilter();

        foreach (static::EXCLUDE_TAG_FILTERS as $excludeTag) {
            $tagFilterValue = new TagFilterValue();

            $tagFilterValue->setTagName($excludeTag);
            $tagFilterValue->setExclude(true);

            $hasTagFilter->setFilterValue($tagFilterValue);
        }

        $orderList->addFilter($hasTagFilter);
    }
}