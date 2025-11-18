<?php

namespace App\Domain\Workflow\Repository;

use PDO;

final class WorkflowAlreadyInUseRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function workflowInUse(string $workflow_id): bool
    {
        $query = "SELECT workflow_id FROM sale WHERE workflow_id = :workflow_id";

        $statement = $this->connection->prepare($query);
        $statement->bindParam(':workflow_id', $workflow_id);
        $statement->execute();
        return $statement->rowCount() > 0;
    }
}
