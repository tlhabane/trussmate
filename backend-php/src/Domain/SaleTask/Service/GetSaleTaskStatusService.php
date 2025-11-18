<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\SaleTask\Data\TaskStatus;

final class GetSaleTaskStatusService
{
    public static function getStatus(string $task_status): TaskStatus
    {
        return match (trim(strtolower($task_status))) {
            'pending' => TaskStatus::PENDING,
            'started' => TaskStatus::STARTED,
            'cancelled' => TaskStatus::CANCELLED,
            'completed' => TaskStatus::COMPLETED,
            default => TaskStatus::TENTATIVE
        };
    }
}
