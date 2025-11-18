<?php

namespace App\Domain\Workflow\Repository;

use PDO;

final class WorkflowTaskIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $workflow_task_id): bool
    {
        $query = 'SELECT workflow_task_id FROM workflow_task WHERE workflow_task_id = :workflow_task_id';
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':workflow_task_id', $workflow_task_id);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
