<?php

namespace App\Domain\UserSession\Repository;

use PDO;

final class EndAllUserSessionsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function endSessions(string $user_id): bool
    {
        $query = "UPDATE user_session SET 
                      session_status = 'ended', 
                      session_expiry = CURRENT_TIMESTAMP
                  WHERE 
                      session_status = 'active' AND 
                      TRIM(LOWER(user_id)) = ?";

        $query_stmt = $this->connection->prepare($query);
        $formattedUserId = trim(strtolower($user_id));

        $query_stmt->bindParam(1, $formattedUserId);
        return $query_stmt->execute();
    }
}
