<?php

namespace App\Domain\SaleTask\Repository;

use PDOStatement;
use PDO;

final class GetSaleTaskStatusLogRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getLog(string $sale_task_id): PDOStatement
    {
        $query = "SELECT 
                      sts.user_id, sts.sale_task_id, sts.task_status, sts.created,
                      uai.first_name, uai.last_name
                  FROM 
                      sale_task_status sts
                  LEFT JOIN 
                      user_account_info uai on sts.user_id = uai.user_id 
                  WHERE 
                      sts.sale_task_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $sale_task_id);
        $query_stmt->execute();
        return $query_stmt;
    }
}
