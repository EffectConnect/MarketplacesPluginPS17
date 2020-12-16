<?php

namespace EffectConnect\Marketplaces\Command;

use EffectConnect\Marketplaces\Exception\OrderImportFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Api\OrderImportApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrderImportCommand
 * @package EffectConnect\Marketplaces\Command
 */
class OrderImportCommand extends ContainerAwareCommand
{
    /**
     * @var OrderImportApi
     */
    protected $_orderImportApi;

    /**
     * OrderImportCommand constructor.
     * @param OrderImportApi $orderImportApi
     */
    public function __construct(
        OrderImportApi $orderImportApi
    ) {
        $this->_orderImportApi = $orderImportApi;
        parent::__construct();
    }

    /**
     * OrderImportCommand configuration.
     */
    protected function configure()
    {
        $this
            ->setName('ec:import-orders')
            ->setDescription('Import orders from EffectConnect Marketplaces.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = Connection::getListActive();

        // For all active connections, import orders.
        if (count($connections) > 0)
        {
            foreach ($connections as $connection) {
                try {
                    $this->_orderImportApi->importOrders($connection);
                } catch (OrderImportFailedException $e) {
                    continue;
                }
            }
        }
    }
}