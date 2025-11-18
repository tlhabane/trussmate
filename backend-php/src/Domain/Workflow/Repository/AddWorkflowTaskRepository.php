<?php

namespace App\Domain\Workflow\Repository;

use App\Domain\Workflow\Data\MapWorkflowQueryData;
use App\Domain\Workflow\Data\WorkflowData;
use PDO;

final class AddWorkflowTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addTask(WorkflowData $data): bool
    {
        $query = "INSERT INTO workflow_task SET 
                  workflow_id = :workflow_id,
                  workflow_task_id = :workflow_task_id,
                  task_id = :task_id,
                  task_no = :task_no,
                  trigger_type = :trigger_type,
                  task_optional = :task_optional,
                  assigned_to = :assigned_to,
                  assignment_note = :assignment_note";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapWorkflowQueryData::map($data);
        $query_data['workflow_id'] = $data->workflow_id;
        return $query_stmt->execute($query_data);
    }
}
