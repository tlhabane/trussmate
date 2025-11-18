<?php

namespace App\Domain\Task\Service;

use App\Util\Utilities;
use PDOStatement;

final class GetFormattedTasksService
{
    public static function getTasks(PDOStatement $tasks): array
    {
        $records = [];
        foreach ($tasks as $task) {
            $records[] = [
                'taskId' => $task['task_id'],
                'taskName' => Utilities::decodeUTF8($task['task_name']),
                'taskDescription' => Utilities::decodeUTF8($task['task_description']),
                'taskAction' => $task['task_action'],
                'taskPayment' => floatval($task['task_payment']),
                'taskPaymentType' => $task['task_payment_type'],
                'taskFrequency' => intval($task['task_frequency']),
                'taskDays' => intval($task['task_days']),
                'taskDocument' => intval($task['task_document'])
            ];
        }
        return $records;
    }
}
