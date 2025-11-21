<?php

namespace App\Domain\TaskMonitor\Data;

enum TaskNotificationType: string
{
    case NOTIFICATION = 'notification';
    case REMINDER = 'reminder';
    case ESCALATION = 'escalation';
    case ESCALATION_REMINDER = 'escalation_reminder';
    case ALL = '';
}
