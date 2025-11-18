<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDO;

final class UpdateEscalationNotificationRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateNotification(TaskMonitorData $data): bool
    {
        $query = "UPDATE escalation_notification SET 
                      username = :username 
                  WHERE 
                      escalation_id = :escalation_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'escalation_id' => $data->escalation_id,
            'username' => $data->username
        ];

        return $query_stmt->execute($query_data);
    }
}
