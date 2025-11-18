<?php

namespace App\Domain\Workflow\Service;

use App\Domain\Workflow\Data\WorkflowData;

final class MapWorkflowDataService
{
    public static function map(array $data): WorkflowData
    {
        $workflowData = new WorkflowData();

        $workflowData->workflow_id = $data['workflowId'];
        $workflowData->workflow_name = $data['workflowName'];
        $workflowData->delivery_required = $data['deliveryRequired'];
        $workflowData->labour_required = $data['labourRequired'];
        $workflowData->search = $data['search'];

        foreach ($data['tasks'] as $task) {
            $taskData = [
                'workflow_task_id' => $task['workflowTaskId'],
                'task_id' => $task['taskId'],
                'task_no' => $task['taskNo'],
                'trigger_type' => $task['triggerType'],
                'task_optional' => $task['taskOptional'],
                'assigned_to' => $task['assignedTo'],
                'assignment_note' => $task['assignmentNote']
            ];
            $workflowData->tasks[] = $taskData;
        }

        return $workflowData;
    }
}
