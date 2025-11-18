<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/customer',
    App\Action\Customer\AddCustomerAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/customer',
    App\Action\Customer\UpdateCustomerAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/customer',
    App\Action\Customer\GetCustomerAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/customer',
    App\Action\Customer\DeleteCustomerAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
