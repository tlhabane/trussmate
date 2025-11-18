<?php

namespace App\Domain\Workflow\Repository;

use PDO;

final class DeleteAllWorkflowTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteTasks(string $workflowId): bool
    {
        $query = "DELETE FROM workflow_task WHERE workflow_id = :workflow_id";
        $query_stmt = $this->connection->prepare($query);
        return $query_stmt->execute(['workflow_id' => $workflowId]);
    }
}
