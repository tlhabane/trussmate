<?php

namespace App\Action\TaskMonitor;

use App\Domain\TaskMonitor\Service\SendTaskNotificationService;
use App\Responder\DefaultStatus422ActionResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Contract\ServerRequestContract;
use App\Routing\ServerResponseHandler;
use App\Factory\ContainerFactory;
use App\Exception\ValidationException;
use Exception;
use PDO;

final class SendTaskNotificationAction
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
            $data = [
                'accountNo' => 'ubzhKcE0tiKWYct82ff8dUoz8KOUnRgW',
                'sessionUsername' => 'cronjob@trussmate.co.za',
                'sessionUserRole' => 'super_admin'
            ];
            $container = (new ContainerFactory())->createInstance();

            $service = new SendTaskNotificationService($container->get(PDO::class));
            $service->sendNotification($data);

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
            ->withStatus($actionResponse['statusCode'] ?? 500)
            ->write(json_encode([
                'error' => $actionResponse['message'],
                'fields' => $actionResponse['fields']
            ]));
    }
}
