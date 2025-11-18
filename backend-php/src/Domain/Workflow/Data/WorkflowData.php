<?php

namespace App\Domain\Workflow\Data;

use App\Domain\User\Data\UserRole;

final class WorkflowData
{
    public string $account_no;
    public string $workflow_id;
    public string $workflow_name;
    public int $delivery_required;
    public int $labour_required;

    public array $tasks;
    public string $workflow_task_id;
    public string $task_id;
    public int $task_no;
    public TaskTriggerType $trigger_type;
    public bool $task_optional;
    public UserRole $assigned_to;
    public string $assignment_note;

    public string $search;
}
