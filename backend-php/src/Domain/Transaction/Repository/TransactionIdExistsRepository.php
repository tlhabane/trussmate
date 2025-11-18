<?php

namespace App\Domain\Transaction\Repository;

use PDO;

final class TransactionIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $transaction_id): bool
    {
        $query = "SELECT transaction_id FROM `transaction` WHERE LOWER(TRIM(transaction_id)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formatted_transaction_id = strtolower(trim($transaction_id));
        $query_stmt->bindParam(1, $formatted_transaction_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
