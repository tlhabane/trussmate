<?php

namespace App\Domain\UserSession\Repository;

use PDO;

final class SessionIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function sessionIdExists(string $session_id): bool
    {
        $query = "SELECT session_id FROM user_session WHERE TRIM(LOWER(session_id)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formattedSessionId = trim(strtolower($session_id));
        $query_stmt->bindParam(1, $formattedSessionId);

        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
