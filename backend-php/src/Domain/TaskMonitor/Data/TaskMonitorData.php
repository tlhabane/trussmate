<?php

namespace App\Domain\TaskMonitor\Data;

final class TaskMonitorData
{
    public string $account_no;
    public string $username;
    public string $escalation_id;
    public EscalationType $escalation_type;
    public int $escalation_days;
    public string $escalation_task_id;
    public string $task_id;
    public array $tasks;
    public string $search;
}
