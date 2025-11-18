<?php

namespace App\Routing;

use App\Util\BasePathDetector;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Router
{
    /**
     * @throws Exception
     */
    public function routeRequest(
        array $server,
        string $request_method,
        string $uri,
        string $callback,
        string|array $middleWare = ''
    ): void {
        try {
            $currentUri = (string)filter_var($server['REQUEST_URI'], FILTER_SANITIZE_URL);
            $basePath = (new BasePathDetector($server))->getBasePath();
            $request_uri = str_replace($basePath, '', $currentUri);

            /* Format Request based on server uri - strip URL query params */
            $requestUriParts = explode('?', $request_uri);
            $updatedRequestUri = $requestUriParts[0] ?? $request_uri;

            /* Format user request uri - strip URL query params */
            $uri_parts = explode('?', $uri);
            $updatedUri = $uri_parts[0] ?? $uri;

            if ($server['REQUEST_METHOD'] === trim($request_method) && $updatedRequestUri === $updatedUri) {
                $formatted_request_method = strtolower(trim($request_method));
                if (is_callable([new HttpActionRequestHandler, $formatted_request_method])) {
                    (new HttpActionRequestHandler)->{$request_method}($callback, $middleWare);
                }
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            echo $e->getMessage();
        }
    }
}
