<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/task',
    App\Action\Task\AddTaskAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/task',
    App\Action\Task\UpdateTaskAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/task',
    App\Action\Task\GetTaskAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
