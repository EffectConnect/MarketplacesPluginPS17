<?php

namespace EffectConnect\Marketplaces\Command;

use EffectConnect\Marketplaces\Exception\CatalogExportFailedException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Api\CatalogExportApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CatalogExportCommand
 * @package EffectConnect\Marketplaces\Command
 */
class CatalogExportCommand extends ContainerAwareCommand
{
    /**
     * @var CatalogExportApi
     */
    protected $_catalogExportApi;

    /**
     * CatalogExportCommand constructor.
     * @param CatalogExportApi $catalogExportApi
     */
    public function __construct(
        CatalogExportApi $catalogExportApi
    ) {
        $this->_catalogExportApi = $catalogExportApi;
        parent::__construct();
    }

    /**
     * CatalogExportCommand configuration.
     */
    protected function configure()
    {
        $this
            ->setName('ec:export-catalog')
            ->setDescription('Export catalog to EffectConnect Marketplaces.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = Connection::getListActive();

        // For all active connections, export catalog.
        if (count($connections) > 0)
        {
            foreach ($connections as $connection) {
                try {
                    $this->_catalogExportApi->exportCatalog($connection);
                } catch (CatalogExportFailedException $e) {
                    continue;
                }
            }
        }
    }
}