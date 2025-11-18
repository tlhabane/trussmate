<?php

namespace App\Responder;

use App\Routing\ServerResponseHandler;

final class DefaultStatus422ActionResponse
{
    public static function getResponse(): array
    {
        return [
            'defaultResponseHandler' => new ServerResponseHandler(),
            'defaultResponse' => [
                'message' => 'An error occurred while processing your request, please try again.',
                'statusCode' => 422,
                'fields' => []
            ]
        ];
    }
}
