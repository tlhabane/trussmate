<?php

namespace App\Domain\SaleTask\Repository;

use App\Domain\SaleTask\Data\SaleTaskData;
use PDO;

final class LogSaleTaskStatusUpdateRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function logStatus(SaleTaskData $data): bool
    {
        $query = "INSERT INTO sale_task_log SET 
                  user_id = :user_id,
                  sale_task_id = :sale_task_id,
                  task_status = :task_status";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'user_id' => $data->user_id,
            'sale_task_id' => $data->sale_task_id,
            'task_status' => $data->task_status->value
        ];

        return $query_stmt->execute($query_data);
    }
}
