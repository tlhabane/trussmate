<?php

namespace App\Domain\Workflow\Repository;

use PDO;

final class WorkflowIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $workflow_id): bool
    {
        $query = 'SELECT workflow_id FROM workflow WHERE workflow_id = :workflow_id';
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':workflow_id', $workflow_id);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
