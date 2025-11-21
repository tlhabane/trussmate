<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDOStatement;
use PDO;

final class GetTaskNotificationRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getNotification(TaskMonitorData $data): PDOStatement
    {
        $query = "SELECT 
                      stn.message_id, stn.notification_type, stn.sale_task_id, t.task_name, t.task_description,
                      t.task_action, mr.recipient_address
                  FROM sale_task_notification stn 
                  LEFT JOIN 
                      sale_task st on stn.sale_task_id = st.sale_task_id 
                  LEFT JOIN 
                      task t on st.task_id = t.task_id
                  LEFT JOIN
                      message_recipient mr on stn.message_id = mr.message_id     
                  WHERE 
                      t.account_no = :account_no";

        $query .= empty($data->sale_task_id) ? "" : " AND stn.sale_task_id = :sale_task_id";
        $query .= empty($data->task_notification_type->value) ? "" : " AND stn.notification_type = :notification_type";
        $query .= empty($data->recipient_address) ? "" : " AND mr.recipient_address = :recipient_address";

        $statement = $this->connection->prepare($query);
        $statement->bindParam(':account_no', $data->account_no);
        if (!empty($data->sale_task_id)) {
            $statement->bindParam(':sale_task_id', $data->sale_task_id);
        }
        if (!empty($data->task_notification_type->value)) {
            $notification_type = $data->task_notification_type->name;
            $statement->bindParam(':notification_type', $notification_type);
        }
        if (!empty($data->recipient_address)) {
            $statement->bindParam(':recipient_address', $data->recipient_address);
        }
        $statement->execute();
        return $statement;
    }
}
