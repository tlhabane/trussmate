<?php

namespace App\Domain\UserSession\Repository;

use App\Domain\UserSession\Data\UserSessionData;
use PDO;

final class AddNewSessionRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addSession(UserSessionData $data): bool
    {
        $query = "INSERT INTO user_session SET 
                  account_no = :account_no,
                  user_id = :user_id,
                  user_role = :user_role,
                  session_id = :session_id,
                  session_hash = :session_hash,
                  session_expiry = :session_expiry,
                  device_id = :device_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'account_no' => $data->account_no,
            'user_id' => $data->user_id,
            'user_role' => $data->user_role->value,
            'session_id' => $data->session_id,
            'session_hash' => $data->session_hash,
            'session_expiry' => $data->session_expiry,
            'device_id' => $data->device_id
        ];

        return $query_stmt->execute($query_data);
    }
}
