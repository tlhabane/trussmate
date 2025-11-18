<?php

namespace App\Domain\Workflow\Repository;

use App\Domain\Workflow\Data\WorkflowData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetWorkflowRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getWorkflow(WorkflowData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      workflow_id, workflow_name 
                  FROM 
                      workflow w 
                  WHERE 
                      w.account_no = :account_no";

        $query .= empty($data->workflow_id) ? "" : " AND w.workflow_id = :workflow_id";
        if (!empty($data->search)) {
            $query .= " AND LOWER(w.workflow_name) LIKE :search";
        }
        $query .= " ORDER BY w.workflow_name ASC";
        $query.= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->workflow_id)) {
            $query_stmt->bindParam(':workflow_id', $data->workflow_id);
        }

        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
