<?php

namespace App\Domain\User\Repository;

use PDO;

final class UserIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function userIdExists(string $user_id): bool
    {
        $query = "SELECT user_id FROM user_account WHERE TRIM(LOWER(user_id)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formattedUserId = trim(strtolower($user_id));
        $query_stmt->bindParam(1, $formattedUserId);

        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
