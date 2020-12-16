<?php

namespace EffectConnect\Marketplaces\Helper;

use DateTime;
use DateTimeZone;
use EffectConnect\Marketplaces\Enums\LoggerProcess;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class LoggerFactory
 * @package EffectConnect\Marketplaces\Factory
 */
class LoggerHelper implements FilePathInterface
{
    /**
     * The log channel for the Monolog logger.
     */
    protected const CHANNEL     = 'EffectConnectMarketplaces';

    /**
     * The time zone for the logs.
     */
    protected const TIME_ZONE   = 'Europe/Amsterdam';

    /**
     * The date format for the date part in the log's filename.
     */
    protected const DATE_FORMAT = 'Y_m_d';

    /**
     * @var Logger[]
     */
    protected static $_loggers = [];

    /**
     * @param string $process
     * @return Logger
     */
    public static function createLogger(string $process = LoggerProcess::OTHER)
    {
        if (isset(static::$_loggers[$process])) {
            return static::$_loggers[$process];
        }

        try {
            static::$_loggers[$process] = new Logger(static::CHANNEL, [
                new StreamHandler(
                    FileHelper::guaranteeFileLocation(
                        static::LOG_DIRECTORY,
                        sprintf(
                            static::LOG_FILENAME_FORMAT,
                            $process,
                            (
                                new DateTime(
                                    'now',
                                    (new DateTimeZone(static::TIME_ZONE))
                                )
                            )->format(static::DATE_FORMAT)
                        )
                    )
                )
            ]);
        } catch (Exception $e) {
            static::$_loggers[$process] = new Logger(static::CHANNEL);
        }

        static::$_loggers[$process]::setTimezone(new DateTimeZone(static::TIME_ZONE));

        return static::$_loggers[$process];
    }
}