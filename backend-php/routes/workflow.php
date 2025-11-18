<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::post(
    '/workflow',
    App\Action\Workflow\AddWorkflowAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::patch(
    '/workflow',
    App\Action\Workflow\UpdateWorkflowAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/workflow',
    App\Action\Workflow\GetWorkflowAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
