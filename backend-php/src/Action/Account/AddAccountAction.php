<?php

namespace App\Action\Account;

use App\Domain\Account\Service\AddAccountService;
use App\Responder\DefaultStatus422ActionResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Contract\ServerRequestContract;
use App\Routing\ServerResponseHandler;
use App\Factory\ContainerFactory;
use App\Exception\ValidationException;
use Exception;
use PDO;

final class AddAccountAction
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

            $accountService = new AddAccountService($container->get(PDO::class));
            $response = $accountService->addAccount($data);

            return $responseHandler
                ->withStatus(201)
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
