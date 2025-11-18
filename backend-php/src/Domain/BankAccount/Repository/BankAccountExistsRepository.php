<?php

namespace App\Domain\BankAccount\Repository;

use App\Domain\BankAccount\Data\BankAccountData;
use PDO;

final class BankAccountExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function accountExists(BankAccountData $data): bool
    {
        $query = "SELECT
                      bank_id
                  FROM 
                      bank_account 
                  WHERE 
                      LOWER(TRIM(bank_name)) = :bank_name AND 
                      LOWER(TRIM(bank_account_no)) = :bank_account_no AND 
                      account_no = :account_no";

        $query_stmt = $this->connection->prepare($query);
        $data->bank_name = strtolower(trim($data->bank_name));
        $query_stmt->bindParam(':bank_name', $data->bank_name);

        $data->bank_account_no = strtolower(trim($data->bank_account_no));
        $query_stmt->bindParam(':bank_account_no', $data->bank_account_no);

        $query_stmt->bindParam(':account_no', $data->account_no);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
