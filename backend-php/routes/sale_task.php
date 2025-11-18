<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::get(
    '/sale/task',
    App\Action\SaleTask\GetSaleTaskListAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/sale/task/status',
    App\Action\SaleTask\UpdateSaleTaskStatusAction::class,
    [
        App\Middleware\SessionValidationMiddleware::class,
        App\Middleware\FileUploadMiddleware::class
    ]
);

HttpRequestVerbHandler::post(
    '/sale/task',
    App\Action\SaleTask\UpdateSaleTaskAction::class,
    [
        App\Middleware\SessionValidationMiddleware::class,
        App\Middleware\FileUploadMiddleware::class
    ]
);
