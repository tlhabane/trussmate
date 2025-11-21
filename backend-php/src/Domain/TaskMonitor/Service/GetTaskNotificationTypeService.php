<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\TaskMonitor\Data\TaskNotificationType;

final class GetTaskNotificationTypeService
{
    public static function getNotificationType(string $notification_type): TaskNotificationType
    {
        return match (strtolower(trim($notification_type))) {
            'notification' => TaskNotificationType::NOTIFICATION,
            'reminder' => TaskNotificationType::REMINDER,
            'escalation' => TaskNotificationType::ESCALATION,
            'escalation_reminder' => TaskNotificationType::ESCALATION_REMINDER,
            default => TaskNotificationType::ALL
        };
    }
}
