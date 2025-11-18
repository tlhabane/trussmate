<?php

namespace App\Domain\Account\Repository;

use PDO;

final class AddAccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addAccountInfo(string $account_no): bool
    {
        $query = "INSERT INTO account SET 
                  account_no = :account_no,
                  account_status = 'active'";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $account_no);
        return $query_stmt->execute();
    }
}
