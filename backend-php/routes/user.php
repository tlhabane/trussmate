<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/login',
    App\Action\User\AuthenticateUserAction::class
);

HttpRequestVerbHandler::post(
    '/user/upload',
    App\Action\User\UploadUserAction::class,
    App\Middleware\FileUploadMiddleware::class
);


HttpRequestVerbHandler::post(
    '/logout',
    App\Action\UserSession\TerminateSessionAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/session/validate',
    App\Action\UserSession\ValidateSessionAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::post(
    '/user',
    App\Action\User\AddUserAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/user',
    App\Action\User\GetUserAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/user/info',
    App\Action\User\UpdateUserAccountInfoAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/user/role',
    App\Action\User\UpdateUserRoleAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/user/status',
    App\Action\User\UpdateUserAccountStatusAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/user/password',
    App\Action\User\UpdateUserHashAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
