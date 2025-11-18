<?php

namespace App\Util;

use App\Factory\LoggerFactory;
use Exception;

final class Logger
{
    /**
     * @param string $logFileName
     * @param string $logMessage
     * @param bool $logError
     * @return void
     */
    public static function addToLog(string $logFileName, string $logMessage, bool $logError = false): void
    {
        try {
            $logger = (new LoggerFactory)
                ->addFileHandler($logFileName)
                ->createLogger();

            if ($logError) {
                $logger->error($logMessage);
            } else {
                $logger->info($logMessage);
            }
        } catch (Exception $exception) {
        }
    }
}
