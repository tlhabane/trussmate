<?php

namespace App\Domain\SaleJob\Repository;

use PDO;

final class DeleteAllSaleJobsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteSaleJob(string $sale_id): bool
    {
        $query = "DELETE FROM sale_job WHERE TRIM(LOWER(sale_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formatted_sale_id = trim(strtolower($sale_id));
        $query_stmt->bindParam(1, $formatted_sale_id);
        return $query_stmt->execute();
    }
}
