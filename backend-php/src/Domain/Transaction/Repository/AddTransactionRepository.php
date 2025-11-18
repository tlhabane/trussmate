<?php

namespace App\Domain\Transaction\Repository;

use App\Domain\Transaction\Data\TransactionData;
use PDO;

final class AddTransactionRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addTransaction(TransactionData $data): bool
    {
        $query = "INSERT INTO `transaction` SET 
                  account_no = :account_no,
                  user_id = :user_id,
                  invoice_no = :invoice_no,            
                  transaction_id = :transaction_id,
                  transaction_amount = :transaction_amount,
                  transaction_type = :transaction_type,
                  transaction_date = :transaction_date,
                  transaction_method = :transaction_method,
                  transaction_desc = :transaction_desc";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'account_no' => $data->account_no,
            'user_id' => $data->user_id,
            'invoice_no' => $data->invoice_no,
            'transaction_id' => $data->transaction_id,
            'transaction_amount' => $data->transaction_amount,
            'transaction_type' => $data->transaction_type->value,
            'transaction_date' => $data->transaction_date,
            'transaction_method' => $data->transaction_method->value,
            'transaction_desc' => $data->transaction_desc,
        ];

        return $query_stmt->execute($query_data);
    }
}
