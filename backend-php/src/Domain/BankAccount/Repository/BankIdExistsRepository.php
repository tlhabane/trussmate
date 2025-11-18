<?php

namespace App\Domain\BankAccount\Repository;

use PDO;

final class BankIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function bankIdExists(string $bankId): bool
    {
        $query = "SELECT bank_id FROM bank_account WHERE bank_id = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(1, $bankId);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
