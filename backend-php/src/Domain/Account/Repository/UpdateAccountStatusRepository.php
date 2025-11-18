<?php

namespace App\Domain\Account\Repository;

use App\Domain\Account\Data\AccountData;
use PDO;

final class UpdateAccountStatusRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateStatus(AccountData $data): bool
    {
        $query = "UPDATE account SET 
                      account_status = :account_status
                  WHERE 
                      account_no = :account_no";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'account_status' => $data->account_status->value,
            'account_no' => $data->account_no
        ];

        return $query_stmt->execute($query_data);
    }
}
