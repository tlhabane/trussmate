<?php

namespace App\Domain\Account\Repository;

use PDOStatement;
use PDO;

final class GetAccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAccount(string $account_no): PDOStatement
    {
        $query = "SELECT 
                      a.account_status, ai.logo, ai.registration_no, ai.registered_name, ai.vat_no, ai.trading_name,
                      ai.tel, ai.alt_tel, ai.email, ai.web, ai.address
                  FROM 
                      account_info ai
                  LEFT JOIN 
                      account a ON a.account_no = ai.account_no
                  WHERE 
                      TRIM(LOWER(ai.account_no)) = ?";

        $query_stmt = $this->connection->prepare($query);
        $formattedAccountNo = trim(strtolower($account_no));
        $query_stmt->bindParam(1, $formattedAccountNo);
        $query_stmt->execute();
        return $query_stmt;
    }
}
