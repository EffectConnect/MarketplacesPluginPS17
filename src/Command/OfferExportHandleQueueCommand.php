<?php

namespace EffectConnect\Marketplaces\Command;

use EffectConnect\Marketplaces\Exception\OfferExportFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Model\OfferExportQueue;
use EffectConnect\Marketplaces\Service\Api\OfferExportApi;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OfferExportHandleQueueCommand
 * @package EffectConnect\Marketplaces\Command
 */
class OfferExportHandleQueueCommand extends ContainerAwareCommand
{
    /**
     * @var OfferExportApi
     */
    protected $_offerExportApi;

    /**
     * OfferExportHandleQueueCommand constructor.
     * @param OfferExportApi $offerExportApi
     */
    public function __construct(
        OfferExportApi $offerExportApi
    ) {
        $this->_offerExportApi = $offerExportApi;
        parent::__construct();
    }

    /**
     * OfferExportHandleQueueCommand configuration.
     */
    protected function configure()
    {
        $this
            ->setName('ec:export-queued-offers')
            ->setDescription('Export queued offers to EffectConnect Marketplaces.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $offerExportQueueItems = OfferExportQueue::getListToExport();
        $connections           = Connection::getListActive();

        // For all active connections, export queued offers.
        if (count($connections) > 0)
        {
            $productIdsToExport = [];
            foreach ($offerExportQueueItems as $offerExportQueueItem) {
                $offerExportQueueItem->exported_at = date('Y-m-d H:i:s', time());
                try {
                    $offerExportQueueItem->save();
                } catch (Exception $e) {
                    continue; // TODO: log?
                }
                $productIdsToExport[$offerExportQueueItem->id_product] = $offerExportQueueItem->id_product;
            }

            if (count($productIdsToExport) > 0)
            {
                foreach ($connections as $connection) {
                    try {
                        $this->_offerExportApi->exportOffers($connection, $productIdsToExport);
                    } catch (OfferExportFailedException $e) {
                        continue;
                    }
                }
            }
        }
    }
}