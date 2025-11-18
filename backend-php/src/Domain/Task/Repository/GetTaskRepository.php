<?php

namespace App\Domain\Task\Repository;

use App\Domain\Task\Data\TaskData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTask(TaskData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      task_id, task_name, task_description, task_payment, task_payment_type, task_days, task_frequency, 
                      task_document, task_action 
                  FROM 
                      task 
                  WHERE 
                      account_no = :account_no";

        $query .= empty($data->task_id) ? "" : " AND task_id = :task_id";
        if (!empty($data->search)) {
            $query .= " AND (
                LOWER(task_name) LIKE :search OR LOWER(task_description) LIKE :search OR
                task_action LIKE :search OR min_payment LIKE :search
            )";
        }
        $query .= " ORDER BY task_name ASC";

        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->task_id)) {
            $query_stmt->bindParam(':task_id', $data->task_id);
        }

        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
