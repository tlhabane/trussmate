<?php

namespace App\Domain\Sale\Repository;

use PDO;

final class SaleIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $sale_id): bool
    {
        $query = "SELECT sale_id FROM sale WHERE TRIM(LOWER(sale_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formatted_sale_id = trim(strtolower($sale_id));
        $query_stmt->bindParam(1, $formatted_sale_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
