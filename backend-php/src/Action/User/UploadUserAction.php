<?php

namespace App\Action\User;

use App\Domain\User\Service\UploadUsers\UploadUserDataFileService;
use App\Responder\DefaultStatus422ActionResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Contract\ServerRequestContract;
use App\Routing\ServerResponseHandler;
use App\Factory\ContainerFactory;
use App\Exception\ValidationException;
use Exception;
use PDO;

final class UploadUserAction
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
            $authorization = $requestHandler->getHeaderLine('authorization');
            list($type, $authDetails) = explode(' ', $authorization);
            if ($type === 'Basic') {
                list($username, $password)    = explode(':', base64_decode($authDetails));
                $uploaded = (array)$requestHandler->getAttributes('files');
                $uploadedFiles = $uploaded['uploadedFiles'] ?? [];
                $container = (new ContainerFactory())->createInstance();
                $userService = new UploadUserDataFileService($container->get(PDO::class));
                $response = $userService->uploadUsers([
                    'username' => $username,
                    'password' => $password,
                    'files' => $uploadedFiles
                ]);

                return $responseHandler
                    ->withStatus(201)
                    ->write(json_encode($response));
            }

            throw new ValidationException('Invalid email\tel\password provided', 403);
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
