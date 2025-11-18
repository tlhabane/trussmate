<?php

namespace App\Domain\SaleTask\Repository;

use App\Domain\SaleTask\Data\SaleTaskData;
use PDO;

final class UpdateSaleTaskStatusRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateStatus(SaleTaskData $data): bool
    {
        $query = "UPDATE sale_task SET
                      task_status = :task_status 
                  WHERE 
                      sale_task_id = :sale_task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'sale_task_id' => $data->sale_task_id,
            'task_status' => $data->task_status->value
        ];

        return $query_stmt->execute($query_data);
    }
}
