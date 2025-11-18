<?php

namespace App\Domain\Messaging\Repository;

use PDO;

final class DeleteMessageRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteMessage(string $messageId): bool
    {
        $query = "DELETE 
                      attachment, message, recipient 
                  FROM 
                      message 
                  LEFT JOIN 
                      message_attachment attachment ON attachment.message_id = message.message_id 
                  LEFT JOIN 
                      message_recipient recipient ON recipient.message_id = message.message_id";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $messageId);
        return $query_stmt->execute();
    }
}
