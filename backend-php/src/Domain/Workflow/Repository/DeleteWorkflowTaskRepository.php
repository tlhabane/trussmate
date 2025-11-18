<?php

namespace App\Domain\Workflow\Repository;

use PDO;

final class DeleteWorkflowTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteTask(string $taskId): bool
    {
        $query = "DELETE FROM workflow_task WHERE workflow_task_id = :task_id";
        $query_stmt = $this->connection->prepare($query);
        return $query_stmt->execute(['task_id' => $taskId]);
    }
}
