<?php

namespace App\Domain\Workflow\Repository;

use PDO;

final class WorkflowNameExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function nameExists(string $account_no, string $workflow_name): bool
    {
        $query = 'SELECT 
                      workflow_name 
                  FROM 
                      workflow 
                  WHERE 
                      account_no = :account_no AND 
                      TRIM(LOWER(workflow_name)) = :workflow_name';

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $account_no);
        $formatted_workflow_name = trim(strtolower($workflow_name));
        $query_stmt->bindParam(':workflow_name', $formatted_workflow_name);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
