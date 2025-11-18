<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/task/monitor',
    App\Action\TaskMonitor\AddTaskMonitorAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/task/monitor',
    App\Action\TaskMonitor\UpdateTaskMonitorAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/task/monitor',
    App\Action\TaskMonitor\GetEscalationTaskMonitorAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::delete(
    '/task/monitor',
    App\Action\TaskMonitor\DeleteTaskMonitorAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
