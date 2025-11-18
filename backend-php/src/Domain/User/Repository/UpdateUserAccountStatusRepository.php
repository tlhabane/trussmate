<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use PDO;

final class UpdateUserAccountStatusRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateStatus(UserData $data): bool
    {
        $query = "UPDATE user_account SET 
                      user_status = :user_status
                  WHERE 
                      user_id = :username";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'username' => $data->username,
            'user_status' => $data->user_status->value
        ];

        return $query_stmt->execute($query_data);
    }
}
