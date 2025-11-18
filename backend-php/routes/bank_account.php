<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/settings/bank',
    App\Action\BankAccount\AddBankAccountAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/settings/bank',
    App\Action\BankAccount\UpdateBankAccountAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/settings/bank',
    App\Action\BankAccount\DeleteBankAccountAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/settings/bank',
    App\Action\BankAccount\GetBankAccountAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
