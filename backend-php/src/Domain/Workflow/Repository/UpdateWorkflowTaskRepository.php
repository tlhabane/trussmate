<?php

namespace App\Domain\Workflow\Repository;

use App\Domain\Workflow\Data\MapWorkflowQueryData;
use App\Domain\Workflow\Data\WorkflowData;
use PDO;

final class UpdateWorkflowTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateTask(WorkflowData $data): bool
    {
        $query = "UPDATE workflow_task SET 
                      task_id = :task_id,   
                      task_no = :task_no,
                      trigger_type = :trigger_type,
                      task_optional = :task_optional,
                      assigned_to = :assigned_to,
                      assignment_note = :assignment_note 
                  WHERE 
                      workflow_task_id = :workflow_task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapWorkflowQueryData::map($data);
        return $query_stmt->execute($query_data);
    }
}
