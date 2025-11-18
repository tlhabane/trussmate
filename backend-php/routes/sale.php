<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/sale',
    App\Action\Sale\AddSaleAction::class,
    [
        App\Middleware\SessionValidationMiddleware::class,
        App\Middleware\FileUploadMiddleware::class
    ]
);

HttpRequestVerbHandler::patch(
    '/sale',
    App\Action\Sale\UpdateSaleAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/sale',
    App\Action\Sale\UpdateSaleAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/sale',
    App\Action\Sale\GetSaleAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/sale/estimate/preview',
    App\Action\Sale\PreviewEstimateAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/sale/quotation/download',
    App\Action\Sale\CreateSaleQuotationAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/sale/quotation/send',
    App\Action\Sale\SendSaleQuotationAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
