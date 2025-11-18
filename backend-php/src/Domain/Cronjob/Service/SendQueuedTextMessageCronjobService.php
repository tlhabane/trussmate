<?php

namespace App\Domain\Cronjob\Service;

use App\Domain\Messaging\Service\SendQueuedTextMessageService;
use App\Exception\RuntimeException;
use App\Util\Utilities;
use PDO;

final class SendQueuedTextMessageCronjobService
{
    private SendQueuedTextMessageService $sendQueuedTextMessageService;

    public function __construct(PDO $connection)
    {
        $this->sendQueuedTextMessageService = new SendQueuedTextMessageService($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function sendTextMessage(array $data): void
    {
        $message_priority = Utilities::sanitizeString($data['messagePriority'] ?? 'medium');
        $this->sendQueuedTextMessageService->sendTextMessage($message_priority);
    }
}
