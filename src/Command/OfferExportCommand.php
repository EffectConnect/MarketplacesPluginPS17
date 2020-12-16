<?php

namespace EffectConnect\Marketplaces\Command;

use EffectConnect\Marketplaces\Exception\OfferExportFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Api\OfferExportApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OfferExportCommand
 * @package EffectConnect\Marketplaces\Command
 */
class OfferExportCommand extends ContainerAwareCommand
{
    /**
     * @var OfferExportApi
     */
    protected $_offerExportApi;

    /**
     * OfferExportCommand constructor.
     * @param OfferExportApi $offerExportApi
     */
    public function __construct(
        OfferExportApi $offerExportApi
    ) {
        $this->_offerExportApi = $offerExportApi;
        parent::__construct();
    }

    /**
     * OfferExportCommand configuration.
     */
    protected function configure()
    {
        $this
            ->setName('ec:export-offers')
            ->setDescription('Export all offers to EffectConnect Marketplaces.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = Connection::getListActive();

        // For all active connections, export offers.
        if (count($connections) > 0)
        {
            foreach ($connections as $connection) {
                try {
                    $this->_offerExportApi->exportOffers($connection);
                } catch (OfferExportFailedException $e) {
                    continue;
                }
            }
        }
    }
}