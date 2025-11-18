<?php

use App\Routing\HttpRequestVerbHandler;

/*HttpRequestVerbHandler::post(
    '/cronjob/send/statement',
    App\Action\CronJob\SendAccountStatementAction::class
);*/

HttpRequestVerbHandler::post(
    '/cronjob/send/email',
    App\Action\Cronjob\SendQueuedEmailMessageAction::class
);

HttpRequestVerbHandler::post(
    '/cronjob/send/text',
    App\Action\Cronjob\SendQueuedTextMessageAction::class
);

HttpRequestVerbHandler::post(
    '/cronjob/file/cleanup',
    App\Action\Cronjob\FileCleanUpAction::class
);
