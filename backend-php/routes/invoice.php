<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::get(
    '/invoice',
    App\Action\Invoice\GetInvoiceAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/invoice',
    App\Action\Invoice\AddInvoiceAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/invoice/download',
    App\Action\Invoice\CreateInvoiceAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/invoice/send',
    App\Action\Invoice\SendInvoiceAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

