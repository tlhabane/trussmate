<?php

namespace App\Domain\Messaging\Repository;

use PDO;

final class AttachmentIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function attachmentIdExists(string $attachmentId): bool
    {
        $query = "SELECT attachment_id FROM message_attachment WHERE attachment_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $attachmentId);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
