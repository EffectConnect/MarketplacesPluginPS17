<?php

namespace EffectConnect\Marketplaces\Helper;

/**
 * Interface FilePathInterface
 * @package EffectConnect\Marketplaces\Helper
 */
interface FilePathInterface
{
    /**
     * The directory where all temporary export and log files are situated.
     */
    public const DATA_DIRECTORY          = __DIR__ . '/../../data/';

    /**
     * The directory where the XML exports needs to be generated (parameter is the content type).
     */
    public const EXPORT_DIRECTORY_FORMAT = self::DATA_DIRECTORY . 'xml/%s/';

    /**
     * The filename for the generated XML (first parameter is the content type, second parameter is the shop ID and third parameter the current timestamp).
     */
    public const EXPORT_FILENAME_FORMAT  = '%s_%s_%s.xml';

    /**
     * The directory where the logs need to be generated.
     */
    public const LOG_DIRECTORY           = self::DATA_DIRECTORY . 'log/';

    /**
     * The filename for the generated log file (first parameter is the process, second parameter is the date).
     */
    public const LOG_FILENAME_FORMAT     = '%s-%s.log';

    /**
     * The download location for zipped data map (parameter is the current timestamp).
     */
    public const ZIP_FILENAME_FORMAT     = 'data_%s.zip';
}