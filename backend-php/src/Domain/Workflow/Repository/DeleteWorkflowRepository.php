<?php

namespace App\Domain\Workflow\Repository;

use PDO;

final class DeleteWorkflowRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteWorkflow(string $workflowId): bool
    {
        $query = "DELETE w, wt 
                  FROM 
                      workflow w 
                  LEFT JOIN 
                      workflow_task wt ON wt.workflow_id = w.workflow_id 
                  WHERE 
                      w.workflow_id = :workflow_id";

        $query_stmt = $this->connection->prepare($query);
        return $query_stmt->execute(['workflow_id' => $workflowId]);
    }
}
