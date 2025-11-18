<?php

namespace App\Domain\BankAccount\Repository;

use App\Domain\BankAccount\Data\BankAccountData;
use PDO;

final class UpdateBankAccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateBankAccount(BankAccountData $bankAccountData): bool
    {
        $query = "UPDATE bank_account SET 
                       bank_name = :bank_name,
                       bank_account_name = :bank_account_name,
                       bank_account_no = :bank_account_no,
                       branch_code = :branch_code 
                   WHERE 
                       bank_id		= :bank_id";

        $stmt = $this->connection->prepare($query);

        $stmt->bindParam(':bank_id', $bankAccountData->bank_id);
        $stmt->bindParam(':bank_name', $bankAccountData->bank_name);
        $stmt->bindParam(':bank_account_name', $bankAccountData->bank_account_name);
        $stmt->bindParam(':bank_account_no', $bankAccountData->bank_account_no);
        $stmt->bindParam(':branch_code', $bankAccountData->branch_code);
        return $stmt->execute();
    }
}
