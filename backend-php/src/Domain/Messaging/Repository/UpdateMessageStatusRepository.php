<?php

namespace App\Domain\Messaging\Repository;

use App\Domain\Messaging\Data\MessagingData;
use PDO;

final class UpdateMessageStatusRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateMessageStatus(MessagingData $data): bool
    {
        $query = "UPDATE message_recipient SET 
                      message_status = :message_status  
                  WHERE 
                      recipient_id = :recipient_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'message_status' => $data->message_status->name,
            'recipient_id' => $data->recipient_id,
        ];

        return $query_stmt->execute($query_data);
    }
}
