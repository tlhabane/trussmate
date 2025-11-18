<?php

namespace App\Domain\Workflow\Service;

use App\Domain\User\Service\GetUserRoleService;
use App\Domain\Workflow\Data\WorkflowData;

final class MapWorkflowTaskDataService
{
    public static function map(array $data): WorkflowData
    {
        $workflowData = new WorkflowData();

        $workflowData->task_id = $data['taskId'];
        $workflowData->task_no = $data['taskNo'];
        $workflowData->trigger_type = GetTaskTriggerTypeService::getTrigger($data['triggerType']);
        $workflowData->task_optional = $data['taskOptional'] === 1;
        $workflowData->assigned_to = GetUserRoleService::getUserRole($data['assignedTo']);
        $workflowData->assignment_note = $data['assignmentNote'];

        return $workflowData;
    }
}
