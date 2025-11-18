<?php

namespace App\Domain\Messaging\Repository;

use App\Domain\Messaging\Data\MessagingData;
use PDO;

final class AddMessageRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addMessage(MessagingData $data): bool
    {
        $query = "INSERT INTO message SET 
                  account_no = :account_no,      
                  user_id = :user_id,
                  message_id = :message_id,
                  message_type = :message_type,
                  message_priority = :message_priority,
                  record_id = :record_id,
                  subject = :subject,
                  message = :message,
                  channel = :channel";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'account_no' => $data->account_no,
            'user_id' => $data->user_id,
            'message_id' => $data->message_id,
            'message_type' => $data->message_type->name ?? 'other',
            'message_priority' => $data->message_priority->name ?? 'medium',
            'record_id' => $data->record_id,
            'subject' => $data->subject,
            'message' => $data->message,
            'channel' => $data->channel->name ?? 'email'
        ];

        return $query_stmt->execute($query_data);
    }
}
