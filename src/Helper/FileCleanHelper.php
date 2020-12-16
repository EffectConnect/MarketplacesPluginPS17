<?php

namespace EffectConnect\Marketplaces\Helper;

use DateTime;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class FileCleanHelper
 * @package EffectConnect\Marketplaces\Helper
 */
class FileCleanHelper implements FilePathInterface
{
    /**
     * Determines after how many days a temp file is deleted.
     */
    public const TMP_FILE_EXPIRATION_DAYS = 7;

    /**
     * File extensions to clean.
     */
    protected const EXTENSIONS_TO_CLEAN   = ['xml', 'log', 'zip'];

    /**
     * @param int $logExpirationDays
     */
    public static function cleanFiles(int $logExpirationDays = self::TMP_FILE_EXPIRATION_DAYS)
    {
        if (!file_exists(static::DATA_DIRECTORY) || !is_dir(static::DATA_DIRECTORY)) {
            return;
        }

        $di = new RecursiveDirectoryIterator(static::DATA_DIRECTORY);
        foreach (new RecursiveIteratorIterator($di) as $filename => $file)
        {
            if (!in_array($file->getExtension(), static::EXTENSIONS_TO_CLEAN)) {
                continue;
            }

            if (!file_exists($filename) || !is_file($filename)) {
                continue;
            }

            try {
                $now  = new DateTime();
                $then = (new DateTime())->setTimestamp($file->getMTime());
                $diff = $then->diff($now);
                $days = intval($diff->format('%r%a'));
            } catch(Exception $e) {
                $days = 0;
            }

            if ($days < $logExpirationDays) {
                continue;
            }

            unlink($filename);
        }
    }
}