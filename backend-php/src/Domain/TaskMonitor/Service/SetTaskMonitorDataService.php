<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\TaskMonitor\Data\TaskMonitorData;

final class SetTaskMonitorDataService
{
    public static function set(array $data): TaskMonitorData
    {
        $taskMonitorData = new TaskMonitorData();
        $taskMonitorData->username = $data['username'];
        $taskMonitorData->escalation_id = $data['escalationId'];
        $taskMonitorData->escalation_task_id = $data['escalationTaskId'];
        $taskMonitorData->escalation_days = $data['escalationDays'];
        $taskMonitorData->escalation_type = GetEscalationTypeService::getType($data['escalationType']);
        $taskMonitorData->task_id = $data['taskId'];
        $taskMonitorData->tasks = $data['tasks'] ?? [];
        $taskMonitorData->search = $data['search'];

        return $taskMonitorData;
    }
}
