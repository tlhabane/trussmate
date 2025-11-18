<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/customer/address',
    App\Action\CustomerAddress\AddCustomerAddressAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/customer/address',
    App\Action\CustomerAddress\UpdateCustomerAddressAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/customer/address',
    App\Action\CustomerAddress\GetCustomerAddressAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/customer/address',
    App\Action\CustomerAddress\DeleteCustomerAddressAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

