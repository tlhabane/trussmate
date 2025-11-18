<?php

namespace App\Domain\Workflow\Repository;

use App\Domain\Workflow\Data\WorkflowData;
use PDO;

final class UpdateWorkflowRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateWorkflow(WorkflowData $data): bool
    {
        $query = "UPDATE workflow SET 
                      workflow_name = :workflow_name
                  WHERE 
                      workflow_id = :workflow_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'workflow_id' => $data->workflow_id,
            'workflow_name' => $data->workflow_name,
        ];
        return $query_stmt->execute($query_data);
    }
}
