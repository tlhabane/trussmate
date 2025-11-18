<?php

namespace App\Routing;

use App\Contract\ServerRequestContract;
use Exception;

final class HttpActionRequestHandler
{
    protected function invokeAction(string $method, ServerRequestContract $request): void
    {
        try {
            if (!is_callable([new $method, 'invoke'])) {
                throw new Exception('Invalid request method provided.');
            }
            (new $method)->invoke($request);
        } catch (Exception $exception) {
            echo json_encode(['message' => $exception->getMessage()]);
            http_response_code(503);
        }
    }

    public function handleRequestWithBody(string $method, string|array $middleWare = ''): void
    {
        try {
            $requestHandler = new ServerRequestDataHandler();
            $contentType = $requestHandler->getHeaderLine('content-type');

            $bodyData = [];
            if (str_starts_with($contentType, 'application/json')) {
                $jsonData = json_decode((string)file_get_contents('php://input'), true);
                $bodyData = is_array($jsonData) ? $jsonData : [];
            } elseif (str_starts_with($contentType, 'application/x-www-form-urlencoded')) {
                $urlEncodedData = (string)file_get_contents('php://input');
                parse_str($urlEncodedData, $bodyData);
            } else {
                // fallback to $_POST (multipart/form-data)
                $bodyData = $_POST;
            }

            $requestHandler->setQueryParams($bodyData);

            if (!empty($_FILES)) {
                $requestHandler->setUploadedFiles($_FILES);
            }

            // invoke middleware
            if ($middleWare) {
                $requestHandler = $this->handleMiddleWare($middleWare, $requestHandler);
            }
            // invoke intended action
            $this->invokeAction($method, $requestHandler);
        } catch (Exception $exception) {
            echo json_encode(['message' => $exception->getMessage()]);
            http_response_code($exception->getCode());
        }
    }

    public function post(string $method, string|array $middleWare = ''): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRequestWithBody($method, $middleWare);
        }
    }

    public function patch(string $method, string|array $middleWare = ''): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            $this->handleRequestWithBody($method, $middleWare);
        }
    }

    public function delete(string $method, string|array $middleWare = ''): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $this->handleRequestWithBody($method, $middleWare);
        }
    }

    private function getUrlQueryParams(array $server): array
    {
        $currentUri = (string)filter_var($server['REQUEST_URI'], FILTER_SANITIZE_URL);
        $uriParts = explode('?', $currentUri);
        $queryParamBits = explode('&', end($uriParts));
        $queryParams = [];
        foreach ($queryParamBits as $queryParamBit) {
            $keyValue = explode('=', $queryParamBit);
            if (count($keyValue) > 1) {
                $queryParams[$keyValue[0]] = $keyValue[1];
            }
        }

        return $queryParams;
    }

    /**
     * @throws Exception
     */
    public function get(string $method, string|array $middleWare = ''): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $requestHandler = new ServerRequestDataHandler();
                if ($middleWare) {
                    $requestHandler = $this->handleMiddleWare($middleWare, $requestHandler);
                }

                $queryParams = $this->getUrlQueryParams($_SERVER);
                $requestHandler->setQueryParams($queryParams);

                $this->invokeAction($method, $requestHandler);
            } catch (Exception $exception) {
                echo json_encode(['message' => $exception->getMessage()]);
                http_response_code($exception->getCode());
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function handleMiddleWare(
        string|array          $middleWare,
        ServerRequestContract $requestHandler
    ): ServerRequestDataHandler
    {
        $handler = $requestHandler;
        $middleWareClasses = is_array($middleWare) ? $middleWare : [$middleWare];

        foreach ($middleWareClasses as $middleWareClass) {
            if (!is_callable([new $middleWareClass, '__invoke'])) {
                throw new Exception('Invalid middleware class provided.');
            }
            $handler = (new $middleWareClass)->__invoke($handler);
        }

        return $handler;
    }
}
