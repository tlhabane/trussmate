<?php

use App\Routing\HttpRequestVerbHandler;

HttpRequestVerbHandler::get(
    '/report/aging',
    App\Action\Report\GetAccountAgingReportAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/report/aging/download',
    App\Action\Report\CreateAccountAgingReportDocumentAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/report/task/analytics',
    App\Action\Report\GetTaskAnalyticsReportAction::class,
    App\Middleware\SessionValidationMiddleware::class
);

HttpRequestVerbHandler::get(
    '/report/balances',
    App\Action\Report\GetAccountBalancesReportAction::class,
    App\Middleware\SessionValidationMiddleware::class
);
