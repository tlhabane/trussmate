<?php

namespace App\Domain\UserSession\Repository;

use App\Domain\UserSession\Data\UserSessionData;
use PDO;

final class UpdateSessionStatusRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateStatus(UserSessionData $data): bool
    {
        $query = "UPDATE user_session SET 
                      session_status = :session_status
                  WHERE 
                      session_id = :session_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'session_status' => $data->session_status->value,
            'session_id' => $data->session_id,
        ];

        return $query_stmt->execute($query_data);
    }
}
