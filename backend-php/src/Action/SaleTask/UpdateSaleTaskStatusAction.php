<?php

namespace App\Action\SaleTask;

use App\Domain\SaleTask\Service\UpdateSaleTaskStatusService;
use App\Responder\DefaultStatus422ActionResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Contract\ServerRequestContract;
use App\Routing\ServerResponseHandler;
use App\Exception\ValidationException;
use App\Factory\ContainerFactory;
use Exception;
use PDO;

final class UpdateSaleTaskStatusAction
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
            $taskService = new UpdateSaleTaskStatusService($container->get(PDO::class));

            $data = $requestHandler->getQueryParams();
            $sessionData = (array)$requestHandler->getAttributes('sessionData');
            $uploaded = (array)$requestHandler->getAttributes('files');
            $serviceData = array_merge($data, $sessionData, [
                'files' => $uploaded['uploadedFiles'] ?? []
            ]);
            $response = $taskService->updateStatus($serviceData);

            return $responseHandler
                ->withStatus(200)
                ->write(json_encode($response));
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
