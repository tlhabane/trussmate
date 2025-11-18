<?php

namespace App\Domain\Cronjob\Service;

use App\Domain\Messaging\Service\SendQueuedEmailMessageService;
use App\Exception\RuntimeException;
use App\Util\Utilities;
use PDO;

final class SendQueuedEmailMessageCronjobService
{
    private SendQueuedEmailMessageService $sendQueuedEmailMessageService;

    public function __construct(PDO $connection)
    {
        $this->sendQueuedEmailMessageService = new SendQueuedEmailMessageService($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function sendEmail(array $data): void
    {
        $message_priority = Utilities::sanitizeString($data['messagePriority'] ?? 'medium');
        $this->sendQueuedEmailMessageService->sendEmail($message_priority);
    }
}
