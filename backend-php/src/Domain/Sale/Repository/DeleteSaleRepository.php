<?php

namespace App\Domain\Sale\Repository;

use PDO;

final class DeleteSaleRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteSale(string $sale_id): bool
    {
        $query = "DELETE FROM sale WHERE TRIM(LOWER(sale_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formatted_sale_id = trim(strtolower($sale_id));
        $query_stmt->bindParam(1, $formatted_sale_id);
        return $query_stmt->execute();
    }
}
