<?php

namespace App\Domain\Cronjob\Service;

use App\Util\Logger;
use Exception;
use DateTime;

final class FileCleanUpService
{
    /**
     * Delete files within a specified folder(s) that are older than x amount of hours
     *
     * @param array $folders
     * @param int $olderThan
     * @return void
     */
    public static function deleteFiles(array $folders = [], int $olderThan = 6): void
    {
        $files_deleted = 0;
        $total_files = 0;

        try {
            foreach ($folders as $folder) {
                $dir_files = scandir($folder);
                if ($dir_files !== false) {
                    $total_files += count($dir_files);
                    foreach ($dir_files as $dir_file) {
                        if ($dir_file != '.' && $dir_file != '..' && $dir_file != 'index.php') {
                            if (file_exists($folder . $dir_file) && is_file($folder . $dir_file)) {
                                if (filemtime($folder . $dir_file) !== false) {
                                    $obj_date_time = new DateTime('NOW');
                                    $file_modification_date = date('Y-m-d H:i:s', filemtime($folder . $dir_file));
                                    $obj_file_modification_date = new DateTime($file_modification_date);
                                    $date_diff = $obj_date_time->diff($obj_file_modification_date, true);
                                    if ($date_diff->h >= $olderThan) {
                                        if (unlink($folder . $dir_file)) {
                                            $files_deleted += 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            Logger::addToLog(
                'file_cleanup_SUCCESS.log',
                sprintf(
                    'File clean up job completed successfully: %s of %s files deleted.',
                    $files_deleted,
                    $total_files
                ),
            );
        } catch (Exception $exception) {
            Logger::addToLog(
                'file_cleanup_ERROR.log',
                sprintf('Error %s: %s', $exception->getCode(), $exception->getMessage()),
                true
            );
        }
    }
}
