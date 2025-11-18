<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use PDO;

final class AddUserAccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addAccount(UserData $data): bool
    {
        $query = "INSERT INTO user_account SET 
                  account_no = :account_no,           
                  user_id = :username,
                  user_role = :user_role,
                  user_hash = :user_hash";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'account_no' => $data->account_no,
            'username' => $data->username,
            'user_role' => $data->user_role->value,
            'user_hash' => $data->user_hash
        ];

        return $query_stmt->execute($query_data);
    }
}
