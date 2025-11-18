<?php

namespace App\Domain\Task\Data;

final class MapTaskQueryData
{
    public static function map(TaskData $data): array
    {
        return [
            'task_id' => $data->task_id,
            'task_name' => $data->task_name,
            'task_description' => $data->task_description,
            'task_payment' => $data->task_payment,
            'task_days' => $data->task_days,
            'task_payment_type' => $data->task_payment_type->value,
            'task_frequency' => $data->task_frequency,
            'task_document' => $data->task_document,
            'task_action' => $data->task_action->value
        ];
    }
}
