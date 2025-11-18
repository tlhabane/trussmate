<?php

namespace App\Domain\SaleDocument\Repository;

use PDO;

final class DeleteAllSaleDocumentsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteDocuments(string $sale_id): bool
    {
        $query = "DELETE FROM sale_document WHERE TRIM(LOWER(sale_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formatted_sale_id = trim(strtolower($sale_id));
        $query_stmt->bindParam(1, $formatted_sale_id);
        return $query_stmt->execute();
    }
}
