<?php

namespace App\Domain\Messaging\Repository;

use App\Domain\Messaging\Data\MessagingData;
use PDO;

final class AddAttachmentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addAttachment(MessagingData $data): bool
    {
        $query = "INSERT INTO message_attachment SET 
                  message_id = :message_id,
                  attachment_id = :attachment_id,
                  filename = :filename,
                  file_source = :file_source";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'message_id' => $data->message_id,
            'attachment_id' => $data->attachment_id,
            'filename' => $data->filename,
            'file_source' => $data->file_source
        ];

        return $query_stmt->execute($query_data);
    }
}
