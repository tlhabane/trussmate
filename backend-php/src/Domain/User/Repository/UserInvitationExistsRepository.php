<?php

namespace App\Domain\User\Repository;

use PDO;

final class UserInvitationExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function userInvited(string $user_id) : bool {
        $query = "SELECT user_id FROM user_invitation WHERE user_id = ?";
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $user_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
