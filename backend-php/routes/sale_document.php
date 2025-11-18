<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::delete(
    '/sale/document',
    App\Action\SaleDocument\DeleteSaleDocumentAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/sale/document/all',
    App\Action\SaleDocument\DeleteAllSaleDocumentsAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/sale/document',
    App\Action\SaleDocument\GetSaleDocumentAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
