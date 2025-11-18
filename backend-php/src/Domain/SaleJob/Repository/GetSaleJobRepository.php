<?php

namespace App\Domain\SaleJob\Repository;

use PDOStatement;
use PDO;

final class GetSaleJobRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getSaleJob(string $sale_id): PDOStatement
    {
        $query = "SELECT 
                      sale_id, job_no, job_description, design_info, line_items, subtotal, 
                      vat, total 
                  FROM 
                      sale_job 
                  WHERE 
                      sale_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $sale_id);
        $query_stmt->execute();
        return $query_stmt;
    }
}
