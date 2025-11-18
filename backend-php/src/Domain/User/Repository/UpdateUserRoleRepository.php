<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use PDO;

final class UpdateUserRoleRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateRole(UserData $data): bool
    {
        $query = "UPDATE user_account SET 
                      user_role = :user_role
                  WHERE 
                      user_id = :username";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'username' => $data->username,
            'user_role' => $data->user_role->value
        ];

        return $query_stmt->execute($query_data);
    }
}
