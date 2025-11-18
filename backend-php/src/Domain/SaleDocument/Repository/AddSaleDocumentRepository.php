<?php

namespace App\Domain\SaleDocument\Repository;

use App\Domain\SaleDocument\Data\SaleDocumentData;
use PDO;

final class AddSaleDocumentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addDocument(SaleDocumentData $data): bool
    {
        $query = "INSERT INTO sale_document SET 
                   user_id = :user_id,           
                   sale_id = :sale_id,
                   sale_task_id = :sale_task_id,
                   doc_id = :doc_id,
                   doc_type = :doc_type,
                   doc_src = :doc_src,
                   doc_name = :doc_name";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'user_id' => $data->user_id,
            'sale_id' => $data->sale_id,
            'sale_task_id' => $data->sale_task_id,
            'doc_id' => $data->doc_id,
            'doc_type' => $data->doc_type->value,
            'doc_src' => $data->doc_src,
            'doc_name' => $data->doc_name
        ];

        return $query_stmt->execute($query_data);
    }
}
