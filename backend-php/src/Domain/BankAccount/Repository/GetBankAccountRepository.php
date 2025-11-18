<?php

namespace App\Domain\BankAccount\Repository;

use App\Domain\BankAccount\Data\BankAccountData;
use PDOStatement;
use PDO;

final class GetBankAccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getBankAccount(BankAccountData $data, $record_start = 0, $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      bank_id, bank_name, bank_account_name, bank_account_no, branch_code 
                  FROM 
                      bank_account  
                  WHERE 
                      account_no = :account_no";

        $query .= empty($data->bank_id) ? "" : " AND bank_id = :bank_id";
        $query .= " ORDER BY bank_name";
        if ($record_start > 0 || $record_limit > 0) {
            if ($record_start > 0 && $record_limit > 0) {
                $query .= " LIMIT :record_start, :record_limit";
            } else {
                $query .= " LIMIT :record_limit";
            }
        }

        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->bank_id)) {
            $stmt->bindParam(':bank_id', $data->bank_id);
        }
        if ($record_start > 0) {
            $stmt->bindParam(':record_start', $record_start, PDO::PARAM_INT);
        }
        if ($record_limit > 0) {
            $stmt->bindParam(':record_limit', $record_limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt;
    }
}
