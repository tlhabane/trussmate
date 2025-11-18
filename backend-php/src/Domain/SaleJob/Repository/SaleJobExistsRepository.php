<?php

namespace App\Domain\SaleJob\Repository;

use PDO;

final class SaleJobExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function jobExists(string $sale_id): bool
    {
        $query = "SELECT sale_id FROM sale_job WHERE TRIM(LOWER(sale_id)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formatted_sale_id = trim(strtolower($sale_id));
        $query_stmt->bindParam(1, $formatted_sale_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
