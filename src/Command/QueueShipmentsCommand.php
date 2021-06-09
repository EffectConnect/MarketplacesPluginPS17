<?php

namespace EffectConnect\Marketplaces\Command;

use EffectConnect\Marketplaces\Service\QueueShipments;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class QueueShipmentsCommand
 *
 * @package EffectConnect\Marketplaces\Command
 */
class QueueShipmentsCommand extends ContainerAwareCommand
{
    /**
     * @var QueueShipments
     */
    protected $_queueShipmentsService;

    /**
     * QueueShipmentsCommand constructor.
     *
     * @param QueueShipments $queueShipmentsService
     */
    public function __construct(
        QueueShipments $queueShipmentsService
    )
    {
        $this->_queueShipmentsService = $queueShipmentsService;
        parent::__construct();
    }

    /**
     * QueueShipmentsCommand configuration.
     */
    protected function configure()
    {
        $this
            ->setName('ec:queue-shipments')
            ->setDescription('Queue tracking numbers and shipments for export to EffectConnect that are not queued by any hook due to external shipment plugins.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_queueShipmentsService->execute();
    }
}