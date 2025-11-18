<?php

namespace App\Domain\Messaging\Repository;

use PDOStatement;
use PDO;

final class GetMessageAttachmentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAttachment(string $message_id): PDOStatement
    {
        $query = "SELECT 
                      message_id, attachment_id, filename, file_source 
                  FROM 
                      message_attachment 
                  WHERE 
                      message_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $message_id);
        $query_stmt->execute();
        return $query_stmt;
    }
}
