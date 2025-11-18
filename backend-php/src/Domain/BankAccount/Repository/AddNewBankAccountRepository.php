<?php

namespace App\Domain\BankAccount\Repository;

use App\Domain\BankAccount\Data\BankAccountData;
use PDO;

final class AddNewBankAccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addNewBankAccount(BankAccountData $data): bool
    {
        $query = "INSERT INTO bank_account SET 
                   account_no = :account_no,
                   bank_id = :bank_id,
                   bank_name = :bank_name,
                   bank_account_name = :bank_account_name,
                   bank_account_no = :bank_account_no,
                   branch_code = :branch_code";

        $stmt = $this->connection->prepare($query);

        $stmt->bindParam(':account_no', $data->account_no);
        $stmt->bindParam(':bank_id', $data->bank_id);
        $stmt->bindParam(':bank_name', $data->bank_name);
        $stmt->bindParam(':bank_account_name', $data->bank_account_name);
        $stmt->bindParam(':bank_account_no', $data->bank_account_no);
        $stmt->bindParam(':branch_code', $data->branch_code);
        return $stmt->execute();
    }
}
