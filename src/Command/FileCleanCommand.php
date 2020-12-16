<?php

namespace EffectConnect\Marketplaces\Command;

use EffectConnect\Marketplaces\Helper\FileCleanHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FileCleanCommand
 * @package EffectConnect\Marketplaces\Command
 */
class FileCleanCommand extends ContainerAwareCommand
{
    /**
     * FileCleanCommand configuration.
     */
    protected function configure()
    {
        $this
            ->setName('ec:clean-files')
            ->setDescription('Clean log files and temporarily catalog XML files for the EffectConnect Marketplaces plugin.')
            ->addArgument('expiration-days', InputArgument::OPTIONAL, 'Determines after how many days a file expires and is deleted (optional - default is ' . FileCleanHelper::TMP_FILE_EXPIRATION_DAYS . ' days).');
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expirationDaysString = $input->getArgument('expiration-days') ?? FileCleanHelper::TMP_FILE_EXPIRATION_DAYS;

        if (!is_numeric($expirationDaysString)) {
            $output->writeln($this->generateOutputMessage(false, 'Expiration days value is not valid (only numeric) value "' . $expirationDaysString . '".'));
        }

        $expirationDays = intval($input->getArgument('expiration-days') ?? FileCleanHelper::TMP_FILE_EXPIRATION_DAYS);

        FileCleanHelper::cleanFiles($expirationDays);

        $output->writeln($this->generateOutputMessage(true, 'Expired tmp files cleaned successfully.'));
    }

    /**
     * Generate a result message.
     *
     * @param bool $success
     * @param string $errorMessage
     * @return string
     */
    protected function generateOutputMessage(bool $success, string $errorMessage = '')
    {
        return sprintf(
                '<fg=%s>[%s]</>: <fg=cyan>[%s]</>',
                ($success ? 'green' : 'red'),
                ($success ? 'SUCCESS' : ' ERROR '),
                'Clean files'
            ) . (!empty($errorMessage) ? ': ' . $errorMessage : '');
    }
}