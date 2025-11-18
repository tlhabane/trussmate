<?php

namespace App\Domain\User\Repository;

use PDO;

final class UserEmailExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function emailExists(string $email): bool
    {
        $query = "SELECT email FROM user_account_info WHERE TRIM(LOWER(email)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formattedEmail = trim(strtolower($email));
        $query_stmt->bindParam(1, $formattedEmail);

        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
