<?php

namespace App\Domain\SaleTask\Repository;

use PDOStatement;
use PDO;

final class GetSaleTaskLogRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getLog(string $sale_task_id): PDOStatement
    {
        $query = "SELECT 
                      stl.user_id, stl.sale_task_id, stl.task_id, stl.task_no, stl.task_status, stl.task_days, 
                      stl.task_frequency, stl.task_completion_date, stl.task_payment, stl.task_payment_type,
                      stl.comments, stl.created, uai.first_name, uai.last_name, t.task_name, t.task_description
                  FROM 
                      sale_task_log stl 
                  LEFT JOIN 
                      task t on stl.task_id = t.task_id         
                  LEFT JOIN         
                      user_account_info uai on stl.user_id = uai.user_id 
                  WHERE 
                      stl.sale_task_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $sale_task_id);
        $query_stmt->execute();
        return $query_stmt;
    }
}
