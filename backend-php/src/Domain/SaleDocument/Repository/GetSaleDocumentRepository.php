<?php

namespace App\Domain\SaleDocument\Repository;

use App\Domain\SaleDocument\Data\SaleDocumentData;
use PDOStatement;
use PDO;

final class GetSaleDocumentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getDocument(SaleDocumentData $data): PDOStatement
    {
        $query = "SELECT 
                      sd.user_id, sd.sale_id, sd.sale_task_id, sd.doc_id, sd.doc_type, sd.doc_src, sd.doc_name,
                      sd.created, uai.first_name, uai.last_name
                  FROM 
                      sale_document sd 
                  LEFT JOIN 
                      sale s on sd.sale_id = s.sale_id 
                  LEFT JOIN 
                      user_account_info uai on sd.user_id = uai.user_id     
                  WHERE 
                      s.account_no = :account_no";

        $query .= empty($data->sale_id) ? "" : " AND sd.sale_id = :sale_id";
        $query .= empty($data->sale_task_id) ? "" : " AND sd.sale_task_id = :sale_task_id";
        $query .= empty($data->doc_id) ? "" : " AND sd.doc_id = :doc_id";
        $query .= empty($data->doc_type->value) ? "" : " AND sd.doc_type = :doc_type";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(":account_no", $data->account_no);
        if (!empty($data->sale_id)) {
            $query_stmt->bindParam(":sale_id", $data->sale_id);
        }
        if (!empty($data->sale_task_id)) {
            $query_stmt->bindParam(":sale_task_id", $data->sale_task_id);
        }
        if (!empty($data->doc_id)) {
            $query_stmt->bindParam(":doc_id", $data->doc_id);
        }
        if (!empty($data->doc_type->value)) {
            $doc_type = $data->doc_type->value;
            $query_stmt->bindParam(":doc_type", $doc_type);
        }
        $query_stmt->execute();
        return $query_stmt;
    }
}
