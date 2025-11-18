<?php

namespace App\Domain\Messaging\Repository;

use App\Domain\Messaging\Data\MessagingData;
use PDO;

final class AddRecipientRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addRecipient(MessagingData $data): bool
    {
        $query = "INSERT INTO message_recipient SET 
                  message_id = :message_id,
                  recipient_id = :recipient_id, 
                  recipient_name = :recipient_name,
                  recipient_address = :recipient_address";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'message_id' => $data->message_id,
            'recipient_id' => $data->recipient_id,
            'recipient_name' => $data->recipient_name,
            'recipient_address' => $data->recipient_address
        ];

        return $query_stmt->execute($query_data);
    }
}
