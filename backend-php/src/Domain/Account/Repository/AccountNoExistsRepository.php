<?php

namespace App\Domain\Account\Repository;

use PDO;

final class AccountNoExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function accountNoExists(string $account_no): bool
    {
        $query = "SELECT account_no FROM account_info WHERE TRIM(LOWER(account_no)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formattedAccountNo = trim(strtolower($account_no));
        $query_stmt->bindParam(1, $formattedAccountNo);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
