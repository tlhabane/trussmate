<?php

namespace App\Util\Files;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Exception\ValidationException;
use App\Factory\ContainerFactory;
use Exception;

final class ValidateFileCreationParams
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws Exception
     */
    public static function validateParams(array $data): array
    {
        if (empty($data['fileName'])) {
            throw new ValidationException('Invalid file name.');
        }

        if (empty($data['fileContent'])) {
            throw new ValidationException('Invalid file content.');
        }


        $folderName = empty($data['fileDir']) ? 'downloads' : $data['fileDir'];
        $container = (new ContainerFactory())->createInstance();
        $folderAttr = $container->get('constants')["{$folderName}"];
        $directory = $folderAttr['directory'];

        if (empty($directory) || !file_exists($directory) || !is_writable($directory)) {
            throw new ValidationException('Invalid file destination');
        }

        $data['fileLink'] = $folderAttr['url'];
        $data['fileDir'] = $directory;
        return $data;
    }
}
