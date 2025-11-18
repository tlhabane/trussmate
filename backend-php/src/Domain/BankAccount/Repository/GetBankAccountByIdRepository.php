<?php

namespace App\Domain\BankAccount\Repository;

use PDOStatement;
use PDO;

final class GetBankAccountByIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getBankAccount(string $bankId): PDOStatement
    {
        $query = "SELECT 
                      bank_id, bank_name, bank_account_name, bank_account_no, branch_code 
                  FROM 
                      bank_account  
                  WHERE 
                      bank_id = :bank_id";

        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':bank_id', $bankId);
        $stmt->execute();
        return $stmt;
    }
}
