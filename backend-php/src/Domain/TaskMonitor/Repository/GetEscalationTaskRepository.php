<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetEscalationTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTask(TaskMonitorData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      et.escalation_id, et.escalation_task_id, et.task_id,
                      em.escalation_days, em.escalation_type, t.task_name, t.task_description, t.task_days, 
                      uai.first_name, uai.last_name, uai.email
                  FROM 
                      escalation_task et 
                  LEFT JOIN 
                      escalation_monitor em ON et.escalation_id = em.escalation_id 
                  LEFT JOIN 
                      escalation_notification en ON em.escalation_id = en.escalation_id 
                  LEFT JOIN 
                      task t ON et.task_id = t.task_id 
                  LEFT JOIN 
                      user_account_info uai ON uai.user_id = en.username
                  WHERE 
                      em.account_no = :account_no";

        $query .= empty($data->escalation_id) ? "" : " AND et.escalation_id = :escalation_id";
        $query .= empty($data->escalation_task_id) ? "" : " AND et.escalation_task_id = :escalation_task_id";
        $query .= empty($data->escalation_type->value) ? "" : " AND em.escalation_type = :escalation_type";
        $query .= empty($data->task_id) ? "" : " AND et.task_id = :task_id";
        $query .= empty($data->username) ? "" : " AND en.username = :username";
        if (!empty($data->search)) {
            $query .= " AND (
                LOWER(t.task_name) LIKE :search OR LOWER(t.task_description) LIKE :search OR 
                LOWER(et.escalation_id) LIKE :search OR LOWER(et.escalation_task_id OR 
                LOWER(uai.first_name) LIKE :search OR LOWER(uai.last_name) LIKE :search OR 
                LOWER(uai.email) LIKE :search
            )";
        }

        $query .= " ORDER BY t.task_name";
        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);

        if (!empty($data->escalation_id)) {
            $query_stmt->bindParam(':escalation_id', $data->escalation_id);
        }
        if (!empty($data->escalation_task_id)) {
            $query_stmt->bindParam(':escalation_task_id', $data->escalation_task_id);
        }
        if (!empty($data->escalation_type->value)) {
            $escalation_type = $data->escalation_type->value;
            $query_stmt->bindParam(':escalation_type', $escalation_type);
        }
        if (!empty($data->task_id)) {
            $query_stmt->bindParam(':task_id', $data->task_id);
        }
        if (!empty($data->username)) {
            $query_stmt->bindParam(':username', $data->username);
        }

        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
