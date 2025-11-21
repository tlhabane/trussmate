<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDO;

final class AddTaskNotificationRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addNotification(TaskMonitorData $data): bool
    {
        $query = "INSERT INTO sale_task_notification SET
                  message_id = :message_id,
                  notification_type = :notification_type,
                  sale_task_id = :sale_task_id";

        $statement = $this->connection->prepare($query);
        $query_data = [
            'message_id' => $data->message_id,
            'notification_type' => $data->task_notification_type->name,
            'sale_task_id' => $data->sale_task_id
        ];

        return $statement->execute($query_data);
    }
}
