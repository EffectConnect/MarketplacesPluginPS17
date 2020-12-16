<?php

namespace EffectConnect\Marketplaces\Helper;

use EffectConnect\Marketplaces\Exception\FileZipCreationFailedException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Class FileDownloadHelper
 * @package EffectConnect\Marketplaces\Helper
 */
class FileDownloadHelper implements FilePathInterface
{
    /**
     * File extensions to zip.
     */
    protected const EXTENSIONS_TO_ZIP = ['xml', 'log'];

    /**
     * @return string
     * @throws FileZipCreationFailedException
     */
    public static function downloadDataFolderZip()
    {
        $zipFileName = static::DATA_DIRECTORY . sprintf(static::ZIP_FILENAME_FORMAT, time());

        if (!file_exists(static::DATA_DIRECTORY) || !is_dir(static::DATA_DIRECTORY)) {
            throw new FileZipCreationFailedException('Data directory not found');
        }

        $zip = new ZipArchive();
        if (true !== $zip->open($zipFileName,  ZipArchive::CREATE)) {
            throw new FileZipCreationFailedException('Failed to open zip archive');
        }

        $di = new RecursiveDirectoryIterator(static::DATA_DIRECTORY);
        foreach (new RecursiveIteratorIterator($di) as $filename => $file)
        {
            if (!in_array($file->getExtension(), self::EXTENSIONS_TO_ZIP)) {
                continue;
            }

            if (!file_exists($filename) || !is_file($filename)) {
                continue;
            }

            // Get real and relative path for current file.
            $filePath     = $file->getRealPath();
            $relativePath = substr($filePath, strlen(realpath(static::DATA_DIRECTORY)) + 1);

            // Add current file to archive.
            if (false === $zip->addFile($filePath, $relativePath)) {
                throw new FileZipCreationFailedException('Failed to add file to zip archive');
            }
        }

        if (false === $zip->close()) {
            throw new FileZipCreationFailedException('Failed to close zip archive');
        }

        if (!file_exists($zipFileName)) {
            throw new FileZipCreationFailedException('No log files to download');
        }

        return $zipFileName;
    }
}