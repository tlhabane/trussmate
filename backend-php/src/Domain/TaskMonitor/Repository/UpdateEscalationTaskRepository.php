<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDO;

final class UpdateEscalationTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateTask(TaskMonitorData $data): bool
    {
        $query = "UPDATE escalation_task SET 
                      task_id = :task_id 
                  WHERE 
                      escalation_task_id = :escalation_task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'escalation_task_id' => $data->escalation_task_id,
            'task_id' => $data->task_id
        ];

        return $query_stmt->execute($query_data);
    }
}
