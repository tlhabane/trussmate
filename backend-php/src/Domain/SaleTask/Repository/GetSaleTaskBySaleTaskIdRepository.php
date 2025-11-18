<?php

namespace App\Domain\SaleTask\Repository;

use PDOStatement;
use PDO;

final class GetSaleTaskBySaleTaskIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTaskBySaleId(string $sale_task_id): PDOStatement
    {
        $query = "SELECT 
                      st.sale_task_id, st.sale_id, st.task_id, st.task_no, st.task_status, st.task_payment, 
                      st.task_payment_type, st.task_days, st.task_frequency, st.task_completion_date, st.created, 
                      s.sale_no, wt.workflow_id, wt.workflow_id, wt.task_optional, wt.trigger_type, wt.assigned_to, 
                      wt.assignment_note, t.task_name, t.task_description, t.task_action, t.task_document,
                      s.customer_id, s.contact_id, i.invoice_no, SUM(IFNULL(tr.transaction_amount, 0)) AS `total_paid`
                  FROM 
                      sale_task st 
                  LEFT JOIN 
                      sale s on st.sale_id = s.sale_id    
                  LEFT JOIN 
                      workflow_task wt on st.task_id = wt.task_id 
                  LEFT JOIN 
                      task t on wt.task_id = t.task_id 
                  LEFT JOIN 
                      invoice i ON i.sale_task_id = st.sale_task_id    
                  LEFT JOIN 
                      `transaction` tr ON tr.invoice_no = i.invoice_no    
                  WHERE 
                      st.sale_task_id = :sale_task_id
                  GROUP BY 
                      st.sale_task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':sale_task_id', $sale_task_id);
        $query_stmt->execute();

        return $query_stmt;
    }
}
