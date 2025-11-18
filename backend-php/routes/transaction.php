<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/transaction',
    App\Action\Transaction\AddTransactionAction::class,
    App\Middleware\SessionValidationMiddleware::class
);


HttpRequestVerbHandler::get(
    '/transaction',
    App\Action\Transaction\GetTransactionAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/transaction',
    App\Action\Transaction\CancelTransactionAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/transaction/statement',
    App\Action\Transaction\CreateTransactionStatementAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/transaction/statement',
    App\Action\Transaction\SendTransactionStatementAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/transaction/download',
    App\Action\Transaction\CreateTransactionDocumentAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/transaction/send',
    App\Action\Transaction\SendTransactionDocumentAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
