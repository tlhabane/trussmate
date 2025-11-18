<?php

namespace App\Domain\SaleDocument\Repository;

use PDO;

final class DeleteSaleDocumentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteDocument(string $doc_id): bool
    {
        $query = "DELETE FROM sale_document WHERE TRIM(LOWER(doc_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formatted_doc_id = trim(strtolower($doc_id));
        $query_stmt->bindParam(1, $formatted_doc_id);
        return $query_stmt->execute();
    }
}
