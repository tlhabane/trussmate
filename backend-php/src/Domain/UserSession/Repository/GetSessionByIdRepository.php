<?php

namespace App\Domain\UserSession\Repository;

use PDOStatement;
use PDO;

final class GetSessionByIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getSession(string $session_id): PDOStatement
    {
        $query = "SELECT 
                     account_no, user_id, user_role, session_id, session_status, session_hash, session_expiry, 
                     device_id 
                  FROM 
                      user_session
                  WHERE 
                      TRIM(LOWER(session_id)) = ?";

        $query_stmt = $this->connection->prepare($query);

        $formattedSessionId = trim(strtolower($session_id));
        $query_stmt->bindParam(1, $formattedSessionId);

        $query_stmt->execute();
        return $query_stmt;
    }
}
