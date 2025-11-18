<?php

namespace App\Domain\Workflow\Data;

class MapWorkflowQueryData
{
    public static function map(WorkflowData $data): array
    {
        return [
            'workflow_task_id' => $data->workflow_task_id,
            'task_id' => $data->task_id,
            'task_no' => $data->task_no,
            'trigger_type' => $data->trigger_type->value,
            'task_optional' => $data->task_optional ? 1 : 0,
            'assigned_to' => $data->assigned_to->value,
            'assignment_note' => $data->assignment_note,
        ];
    }
}
