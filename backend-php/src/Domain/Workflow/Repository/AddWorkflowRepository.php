<?php

namespace App\Domain\Workflow\Repository;

use App\Domain\Workflow\Data\WorkflowData;
use PDO;

final class AddWorkflowRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addWorkflow(WorkflowData $data): bool
    {
        $query = "INSERT INTO workflow SET 
                  account_no = :account_no,
                  workflow_id = :workflow_id,
                  workflow_name = :workflow_name,
                  delivery_required = :delivery_required,
                  labour_required = :labour_required";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'account_no' => $data->account_no,
            'workflow_id' => $data->workflow_id,
            'workflow_name' => $data->workflow_name,
            'delivery_required' => $data->delivery_required,
            'labour_required' => $data->labour_required,
        ];
        return $query_stmt->execute($query_data);
    }
}
