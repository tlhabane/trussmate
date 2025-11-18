<?php

namespace App\Domain\Transaction\Repository;

use PDO;

final class CancelTransactionRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function cancelTransaction(string $transaction_id, int $transaction_cancelled = 1): bool
    {
        $query = "UPDATE `transaction` tr SET 
                      tr.transaction_cancelled = :transaction_cancelled 
                  WHERE 
                      tr.transaction_id = :transaction_id";
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(":transaction_id", $transaction_id);
        $query_stmt->bindParam(":transaction_cancelled", $transaction_cancelled, PDO::PARAM_INT);
        return $query_stmt->execute();
    }
}
