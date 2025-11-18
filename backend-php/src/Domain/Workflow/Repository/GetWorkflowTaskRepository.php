<?php

namespace App\Domain\Workflow\Repository;

use PDOStatement;
use PDO;

final class GetWorkflowTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTask(string $workflow_id): PDOStatement
    {
        $query = "SELECT 
                      wt.workflow_task_id, wt.task_id, wt.task_no, wt.trigger_type, wt.task_optional, wt.assigned_to,
                      wt.assignment_note, t.task_name, t.task_description, t.task_days, t.task_frequency, 
                      t.task_payment, task_payment_type, t.task_action, wt.workflow_id, w.workflow_name
                  FROM
                      workflow_task wt
                  LEFT JOIN    
                      workflow w ON wt.workflow_id = w.workflow_id 
                  LEFT JOIN 
                      task t on wt.task_id = t.task_id
                  WHERE 
                      wt.workflow_id = :workflow_id
                  ORDER BY 
                      wt.task_no";


        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':workflow_id', $workflow_id);
        $query_stmt->execute();
        return $query_stmt;
    }
}
