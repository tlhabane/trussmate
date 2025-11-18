<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/customer/contact',
    App\Action\ContactPerson\AddContactPersonAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/customer/contact',
    App\Action\ContactPerson\UpdateContactPersonAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/customer/contact',
    App\Action\ContactPerson\GetContactPersonAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/customer/contact',
    App\Action\ContactPerson\DeleteContactPersonAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
