<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDO;

final class AddEscalationTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addTask(TaskMonitorData $data): bool
    {
        $query = "INSERT INTO escalation_task SET 
                  escalation_id = :escalation_id,
                  escalation_task_id = :escalation_task_id,
                  task_id = :task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'escalation_id' => $data->escalation_id,
            'escalation_task_id' => $data->escalation_task_id,
            'task_id' => $data->task_id
        ];

        return $query_stmt->execute($query_data);
    }
}
