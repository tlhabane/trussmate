<?php

namespace App\Domain\Transaction\Repository;

use PDO;

final class DeleteTransactionRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteTransaction(string $transaction_id): bool
    {
        $query = "DELETE FROM `transaction` tr WHERE tr.transaction_id = ?";
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $transaction_id);
        return $query_stmt->execute();
    }
}
