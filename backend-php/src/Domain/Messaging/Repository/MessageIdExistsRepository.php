<?php

namespace App\Domain\Messaging\Repository;

use PDO;

final class MessageIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function messageIdExists(string $messageId): bool
    {
        $query = "SELECT message_id FROM message WHERE message_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $messageId);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
