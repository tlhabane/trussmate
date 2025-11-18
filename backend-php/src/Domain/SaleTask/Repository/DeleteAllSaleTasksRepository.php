<?php

namespace App\Domain\SaleTask\Repository;

use PDO;

final class DeleteAllSaleTasksRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteTask(string $sale_id): bool
    {
        $query = "DELETE
                      st, stl 
                  FROM 
                      sale_task st 
                  LEFT JOIN 
                      sale_task_log stl ON stl.sale_task_id = st.sale_task_id 
                  WHERE 
                      TRIM(LOWER(sale_id)) = ?";

        $query_stmt = $this->connection->prepare($query);
        $formatted_sale_id = trim(strtolower($sale_id));
        $query_stmt->bindParam(1, $formatted_sale_id);
        return $query_stmt->execute();
    }
}
