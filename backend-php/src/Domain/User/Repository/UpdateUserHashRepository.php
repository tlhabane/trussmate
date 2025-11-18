<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use PDO;

final class UpdateUserHashRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateHash(UserData $data): bool
    {
        $query = "UPDATE user_account SET 
                      user_hash = :user_hash
                  WHERE 
                      user_id = :username";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'username' => $data->username,
            'user_hash' => $data->user_hash
        ];

        return $query_stmt->execute($query_data);
    }
}
