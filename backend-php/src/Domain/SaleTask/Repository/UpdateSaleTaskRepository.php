<?php

namespace App\Domain\SaleTask\Repository;

use App\Domain\SaleTask\Data\SaleTaskData;
use PDO;

final class UpdateSaleTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateTask(SaleTaskData $data): bool
    {
        $query = "UPDATE sale_task SET
                      task_id = :task_id,
                      task_no = :task_no,   
                      task_status = :task_status,
                      task_days = :task_days,
                      task_completion_date = :task_completion_date,
                      task_frequency = :task_frequency,
                      task_payment = :task_payment,
                      task_payment_type = :task_payment_type
                  WHERE 
                      sale_task_id = :sale_task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'sale_task_id' => $data->sale_task_id,
            'task_id' => $data->task_id,
            'task_no' => $data->task_no,
            'task_status' => $data->task_status->value,
            'task_days' => $data->task_days,
            'task_completion_date' => $data->task_completion_date,
            'task_frequency' => $data->task_frequency,
            'task_payment' => $data->task_payment,
            'task_payment_type' => $data->task_payment_type->value
        ];

        return $query_stmt->execute($query_data);
    }
}
