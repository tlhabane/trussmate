<?php

namespace App\Domain\User\Repository;

use PDO;

final class DeleteUserRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteUser(string $username): bool
    {
        $query = "DELETE 
                      ua, uai, ui 
                  FROM 
                      user_account ua 
                  LEFT JOIN 
                      user_account_info uai on ua.user_id = uai.user_id 
                  LEFT JOIN
                      user_invitation ui on ua.user_id = ui.user_id     
                  WHERE 
                      ua.user_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $username);
        return $query_stmt->execute();
    }
}
