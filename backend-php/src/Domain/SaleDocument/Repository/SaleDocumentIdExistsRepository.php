<?php

namespace App\Domain\SaleDocument\Repository;

use PDO;

final class SaleDocumentIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $doc_id): bool
    {
        $query = "SELECT doc_id FROM sale_document WHERE TRIM(LOWER(doc_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formatted_doc_id = trim(strtolower($doc_id));
        $query_stmt->bindParam(1, $formatted_doc_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
