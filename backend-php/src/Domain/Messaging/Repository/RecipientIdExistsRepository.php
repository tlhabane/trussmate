<?php

namespace App\Domain\Messaging\Repository;

use PDO;

final class RecipientIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function recipientIdExists(string $recipientId): bool
    {
        $query = "SELECT recipient_id FROM message_recipient WHERE recipient_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $recipientId);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
