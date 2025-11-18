<?php

namespace App\Domain\SaleTask\Repository;

use PDO;

final class SaleTaskIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $sale_task_id): bool
    {
        $query = "SELECT sale_task_id FROM sale_task WHERE TRIM(LOWER(sale_task_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formatted_sale_task_id = trim(strtolower($sale_task_id));
        $query_stmt->bindParam(1, $formatted_sale_task_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
