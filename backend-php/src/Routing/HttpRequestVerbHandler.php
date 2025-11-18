<?php

namespace App\Routing;

use Exception;

final class HttpRequestVerbHandler
{
    private function forwardRequest(
        array $server,
        string $requestMethod,
        string $uri,
        string $callback,
        string|array $middleWare
    ): void {
        try {
            if (!is_callable([new Router, 'routeRequest'])) {
                throw new Exception('Invalid request method provided.');
            }
            (new Router)->routeRequest($server, $requestMethod, $uri, $callback, $middleWare);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public static function post(string $uri, string $callback, string|array $middleWare = ''): void
    {
        (new HttpRequestVerbHandler)->forwardRequest($_SERVER, 'POST', $uri, $callback, $middleWare);
    }

    public static function patch(string $uri, string $callback, string|array $middleWare = ''): void
    {
        (new HttpRequestVerbHandler)->forwardRequest($_SERVER, 'PATCH', $uri, $callback, $middleWare);
    }

    public static function get(string $uri, string $callback, string|array $middleWare = ''): void
    {
        (new HttpRequestVerbHandler)->forwardRequest($_SERVER, 'GET', $uri, $callback, $middleWare);
    }

    public static function delete(string $uri, string $callback, string|array $middleWare = ''): void
    {
        (new HttpRequestVerbHandler)->forwardRequest($_SERVER, 'DELETE', $uri, $callback, $middleWare);
    }
}
