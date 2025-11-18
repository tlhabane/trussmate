<?php

namespace App\Domain\TaskMonitor\Repository;

use PDO;

final class DeleteEscalationNotificationRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteNotification(string $escalation_id): bool
    {
        $query = "DELETE FROM escalation_notification WHERE escalation_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $escalation_id);
        return $query_stmt->execute();
    }
}
