<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDO;

final class AddEscalationNotificationRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addNotification(TaskMonitorData $data): bool
    {
        $query = "INSERT INTO escalation_notification SET 
                  escalation_id = :escalation_id,
                  username = :username";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'escalation_id' => $data->escalation_id,
            'username' => $data->username
        ];

        return $query_stmt->execute($query_data);
    }
}
