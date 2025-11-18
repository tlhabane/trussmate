<?php

namespace App\Action\Cronjob;

use App\Domain\Cronjob\Service\SendQueuedEmailMessageCronjobService;
use App\Responder\DefaultStatus422ActionResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Contract\ServerRequestContract;
use App\Routing\ServerResponseHandler;
use App\Exception\ValidationException;
use App\Factory\ContainerFactory;
use Exception;
use PDO;

final class SendQueuedEmailMessageAction
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
            $data = $requestHandler->getQueryParams();
            $container = (new ContainerFactory())->createInstance();
            $cronJobService = new SendQueuedEmailMessageCronjobService($container->get(PDO::class));
            $cronJobService->sendEmail($data);

            return $responseHandler->withStatus(200);
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
            ->withStatus($actionResponse['statusCode'])
            ->write(json_encode([
                'error' => $actionResponse['message'],
                'fields' => $actionResponse['fields']
            ]));
    }
}
