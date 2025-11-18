<?php

namespace App\Domain\Task\Repository;

use App\Domain\Task\Data\TaskData;
use App\Domain\Task\Data\MapTaskQueryData;
use PDO;

final class UpdateTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateTask(TaskData $data): bool
    {
        $query = "UPDATE task SET 
                      task_name = :task_name,
                      task_description = :task_description,
                      task_payment = :task_payment,
                      task_payment_type = :task_payment_type,
                      task_days = :task_days,
                      task_frequency = :task_frequency,
                      task_document = :task_document,
                      task_action = :task_action 
                  WHERE 
                      task_id = :task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapTaskQueryData::map($data);
        return $query_stmt->execute($query_data);
    }
}
