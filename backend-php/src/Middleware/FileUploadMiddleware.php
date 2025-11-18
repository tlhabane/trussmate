<?php

namespace App\Middleware;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Routing\ServerRequestDataHandler;
use App\Exception\RuntimeException;
use App\Factory\ContainerFactory;
use App\Util\Utilities;
use Exception;

class FileUploadMiddleware
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws RuntimeException
     */
    public function __invoke(ServerRequestDataHandler $request): ServerRequestDataHandler
    {
        $files = $request->getUploadedFiles();
        $uploadedFiles = [];

        foreach ($files as $key => $fileData) {
            // Handle multiple files under one key (e.g. "file[]")
            if (is_array($fileData['name'])) {
                for ($i = 0; $i < count($fileData['name']); $i++) {
                    $tmpPath = $fileData['tmp_name'][$i];
                    $filename = basename($fileData['name'][$i]);
                    $destination = $this->uploadFile($tmpPath, $filename);
                    $uploadedFiles[] = [
                        'filename' => $filename,
                        'source' => $destination
                    ];
                }
            } else {
                // Handle single file
                $tmpPath = $fileData['tmp_name'];
                $filename = basename($fileData['name']);
                $destination = $this->uploadFile($tmpPath, $filename);
                $uploadedFiles[] = [
                    'filename' => $filename,
                    'source' => $destination
                ];
            }
        }

        // Store uploaded file paths as a request attribute
        $request->setAttributes(['uploadedFiles' => $uploadedFiles], 'files');

        return $request;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    private function getDestinationFileName(string $tmpFilename): string
    {
        $extension  = pathinfo($tmpFilename, PATHINFO_EXTENSION);
        $container = (new ContainerFactory())->createInstance();
        $uploads = $container->get('constants')['uploads'];

        do {
            $basename = $uploads['directory'] . Utilities::generateToken();
            $filename = sprintf('%s.%0.8s', $basename, $extension);
        } while (empty($filename) || file_exists($filename));

        return $filename;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws RuntimeException
     */
    private function uploadFile(mixed $tmpPath, mixed $filename): string
    {
        $destination = $this->getDestinationFileName($filename);

        if (is_uploaded_file($tmpPath)) {
            move_uploaded_file($tmpPath, $destination);
            return $destination;
        }

        throw new RuntimeException(
            sprintf('Oops! An error occurred while uploading %s, please try again.', $filename)
        );
    }
}
