<?php

namespace App\Domain\Messaging\Repository;

use App\Domain\Messaging\Data\MessagingData;
use PDO;

final class AddServiceProviderMessageIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addMessageId(MessagingData $data): bool
    {
        $query = "UPDATE message SET 
                      sp_message_id = :sp_message_id 
                  WHERE 
                      message_id = :message_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'sp_message_id' => $data->sp_message_id,
            'message_id' => $data->message_id,
        ];

        return $query_stmt->execute($query_data);
    }
}
