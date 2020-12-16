<?php

namespace EffectConnect\Marketplaces\Command;

use EffectConnect\Marketplaces\Exception\TrackingExportFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Api\TrackingExportApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TrackingExportCommand
 * @package EffectConnect\Marketplaces\Command
 */
class TrackingExportCommand extends ContainerAwareCommand
{
    /**
     * @var TrackingExportApi
     */
    protected $_trackingExportApi;

    /**
     * TrackingExportCommand constructor.
     * @param TrackingExportApi $trackingExportApi
     */
    public function __construct(
        TrackingExportApi $trackingExportApi
    ) {
        $this->_trackingExportApi = $trackingExportApi;
        parent::__construct();
    }

    /**
     * TrackingExportCommand configuration.
     */
    protected function configure()
    {
        $this
            ->setName('ec:export-tracking-numbers')
            ->setDescription('Export tracking numbers to EffectConnect Marketplaces.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = Connection::getListActive();

        // For all active connections, export tracking numbers.
        if (count($connections) > 0)
        {
            foreach ($connections as $connection) {
                try {
                    $this->_trackingExportApi->exportTrackingNumbers($connection);
                } catch (TrackingExportFailedException $e) {
                    continue;
                }
            }
        }
    }
}