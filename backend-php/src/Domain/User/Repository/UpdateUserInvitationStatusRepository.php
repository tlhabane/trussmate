<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\UserData;
use PDO;

final class UpdateUserInvitationStatusRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function inviteUser(UserData $data) : bool {
        $query = "UPDATE user_invitation SET
                      invitation_status = :invitation_status, 
                  WHERE 
                      user_id = :username";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'invitation_status' => $data->invitation_status->value,
            'username' => $data->username
        ];

        return $query_stmt->execute($query_data);
    }
}
