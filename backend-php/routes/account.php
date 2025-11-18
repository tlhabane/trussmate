<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/account',
    App\Action\Account\AddAccountAction::class
);

HttpRequestVerbHandler::post(
    '/account/info',
    App\Action\Account\AddUpdateAccountInfoAction::class,
    [
        App\Middleware\SessionValidationMiddleware::class,
        App\Middleware\FileUploadMiddleware::class
    ]
);

HttpRequestVerbHandler::patch(
    '/account/info',
    App\Action\Account\AddUpdateAccountInfoAction::class,
    [
        App\Middleware\SessionValidationMiddleware::class,
        App\Middleware\FileUploadMiddleware::class
    ]
);

HttpRequestVerbHandler::get(
    '/account/info',
    App\Action\Account\GetAccountInfoAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
