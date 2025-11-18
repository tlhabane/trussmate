<?php

namespace App\Action\Cronjob;

use App\Domain\Cronjob\Service\FileCleanUpService;
use App\Responder\DefaultStatus422ActionResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Contract\ServerRequestContract;
use App\Routing\ServerResponseHandler;
use App\Exception\ValidationException;
use App\Factory\ContainerFactory;
use Exception;

final class FileCleanUpAction
{
    /**
     * @throws Exception
     */
    public function invoke(ServerRequestContract $requestHandler): ServerResponseHandler
    {
        $defaultResponse = DefaultStatus422ActionResponse::getResponse();
        $responseHandler = $defaultResponse['defaultResponseHandler'];
        $actionResponse = $defaultResponse['defaultResponse'];

        try {
            $container = (new ContainerFactory())->createInstance();
            $downloads = $container->get('constants')['downloads'];
            $attachments = $container->get('constants')['attachments'];
            $directories = [
                $downloads['directory'],
                $attachments['directory']
            ];
            // Delete files older than 48 hours
            FileCleanUpService::deleteFiles($directories, 48);

            return $responseHandler->withStatus(201);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $exception) {
            $actionResponse['message'] = $exception->getMessage();
            $actionResponse['statusCode'] = $exception->getCode();
        } catch (ValidationException $exception) {
            $actionResponse = [
                'message' => $exception->getMessage(),
                'statusCode' => $exception->getCode(),
                'fields' => $exception->getErrors()
            ];
        }

        return $responseHandler
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($actionResponse['statusCode'] ?? 500)
            ->write(json_encode([
                'error' => $actionResponse['message'],
                'fields' => $actionResponse['fields']
            ]));
    }
}
