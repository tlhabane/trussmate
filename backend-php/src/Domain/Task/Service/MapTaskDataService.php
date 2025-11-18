<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Data\TaskData;

final class MapTaskDataService
{
    public static function map(array $data): TaskData
    {
        $task_data = new TaskData();

        $task_data->task_id = $data['taskId'];
        $task_data->task_name = $data['taskName'];
        $task_data->task_description = $data['taskDescription'];
        $task_data->task_action = GetTaskActionService::getAction($data['taskAction']);
        $task_data->task_payment = $data['taskPayment'];
        $task_data->task_payment_type = GetTaskPaymentTypeService::getType($data['taskPaymentType']);
        $task_data->task_days = $data['taskDays'];
        $task_data->task_frequency = $data['taskFrequency'];
        $task_data->task_document = $data['taskDocument'];
        $task_data->search = $data['search'];

        return $task_data;
    }
}
