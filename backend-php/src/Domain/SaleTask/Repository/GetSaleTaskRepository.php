<?php

namespace App\Domain\SaleTask\Repository;

use App\Domain\SaleTask\Data\SaleTaskData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetSaleTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTask(SaleTaskData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      st.sale_task_id, st.sale_id, st.task_id, st.task_no, st.task_status, st.task_payment, 
                      st.task_payment_type, st.task_completion_date, 
                      IF(st.task_days > 0, st.task_days, t.task_days) `task_days`, st.task_frequency, st.created,
                      s.sale_no, wt.workflow_id, wt.workflow_id, wt.task_id, wt.task_no, wt.task_optional,
                      wt.trigger_type, wt.assigned_to, wt.assignment_note, t.task_name, t.task_description,
                      t.task_action, t.task_document, s.customer_id, s.contact_id, i.invoice_no,
                      SUM(IFNULL(tr.transaction_amount, 0)) AS `total_paid`, c.customer_name, cp.first_name, 
                      cp.last_name
                  FROM 
                      sale_task st 
                  LEFT JOIN 
                      sale s on st.sale_id = s.sale_id 
                  LEFT JOIN 
                      customer c ON s.customer_id = c.customer_id 
                  LEFT JOIN 
                      contact cp ON s.contact_id = cp.contact_id         
                  LEFT JOIN 
                      workflow_task wt on st.task_id = wt.task_id 
                  LEFT JOIN 
                      task t on wt.task_id = t.task_id 
                  LEFT JOIN 
                      invoice i ON i.sale_task_id = st.sale_task_id    
                  LEFT JOIN 
                      `transaction` tr ON tr.invoice_no = i.invoice_no   
                  WHERE 
                      t.account_no = :account_no";

        $query .= empty($data->user_role->value) ? "" : " AND wt.assigned_to = :user_role";
        $query .= empty($data->task_status->value) ? "" : " AND st.task_status = :task_status";
        $query .= empty($data->sale_id) ? "" : " AND st.sale_id = :sale_id";
        $query .= empty($data->sale_task_id) ? "" : " AND st.sale_task_id = :sale_task_id";
        $query .= empty($data->task_id) ? "" : " AND st.task_id = :task_id";
        $query .= empty($data->task_action->value) ? "" : " AND t.task_action = :task_action";
        $query .= empty($data->customer_id) ? "" : " AND s.customer_id = :customer_id";
        if (!empty($data->search)) {
            $query .= " AND (
                LOWER(wt.trigger_type) LIKE :search OR LOWER(wt.assigned_to) LIKE :search OR 
                LOWER(wt.assignment_note) LIKE :search OR LOWER(t.task_name) LIKE :search OR 
                LOWER(t.task_description) LIKE :search
            )";
        }
        $query .= " GROUP BY st.sale_task_id ORDER BY st.created, st.task_no DESC";
        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);


        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->sale_id)) {
            $query_stmt->bindParam(':sale_id', $data->sale_id);
        }
        if (!empty($data->sale_task_id)) {
            $query_stmt->bindParam(':sale_task_id', $data->sale_task_id);
        }
        if (!empty($data->task_id)) {
            $query_stmt->bindParam(':task_id', $data->task_id);
        }
        if (!empty($data->customer_id)) {
            $query_stmt->bindParam(':customer_id', $data->customer_id);
        }
        if (!empty($data->task_status->value)) {
            $task_status = $data->task_status->name;
            $query_stmt->bindParam(':task_status', $task_status);
        }
        if (!empty($data->task_action->value)) {
            $task_action = $data->task_action->value;
            $query_stmt->bindParam(':task_action', $task_action);
        }
        if (!empty($data->user_role->value)) {
            $user_role = $data->user_role->name;
            $query_stmt->bindParam(':user_role', $user_role);
        }

        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
