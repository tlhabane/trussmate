<?php

namespace App\Domain\BankAccount\Repository;

use PDO;

final class DeleteBankAccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteBankAccount(string $bankId): bool
    {
        $query = "DELETE FROM bank_account WHERE bank_id = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(1, $bankId);
        return $stmt->execute();
    }
}
