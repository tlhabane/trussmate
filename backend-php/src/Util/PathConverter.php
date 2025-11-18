<?php

namespace App\Util;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Factory\ContainerFactory;
use Exception;

class PathConverter
{
    private array $constants = [];

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    public function __construct()
    {
        $container = (new ContainerFactory())->createInstance();
        $this->constants = $container->get('constants');
    }

    public function getFileUrl(string $filepath): string
    {
        $file_parts = explode($this->constants['directory_separator'], $filepath);
        $filename = array_pop($file_parts);
        $directories = ['downloads', 'uploads'];
        foreach ($directories as $directory) {
            if (in_array($directory, $file_parts)) {
                return $this->constants[$directory]['url'] . "/{$filename}";
            }
        }
        return '';
    }

    public function getFilePath(string $fileUrl): string
    {
        $file_parts = explode('/', $fileUrl);
        $filename = array_pop($file_parts);
        $directories = ['downloads', 'uploads'];
        foreach ($directories as $directory) {
            if (in_array($directory, $file_parts)) {
                return $this->constants[$directory]['directory'] . $this->constants['directory_separator'] . $filename;
            }
        }
        return '';
    }
}
