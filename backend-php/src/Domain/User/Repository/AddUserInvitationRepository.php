<?php

namespace App\Domain\User\Repository;

use PDO;

final class AddUserInvitationRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function inviteUser(string $user_id) : bool {
        $query = "INSERT INTO user_invitation SET user_id = ?";
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $user_id);
        return $query_stmt->execute();
    }
}
