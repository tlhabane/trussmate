<?php

namespace App\Domain\UserSession\Repository;

use App\Domain\UserSession\Data\UserSessionData;
use PDO;

final class UpdateSessionExpiryRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateExpiry(UserSessionData $data): bool
    {
        $query = "UPDATE user_session SET 
                      session_expiry = :session_expiry
                  WHERE 
                      session_id = :session_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'session_expiry' => $data->session_expiry,
            'session_id' => $data->session_id,
        ];

        return $query_stmt->execute($query_data);
    }
}
