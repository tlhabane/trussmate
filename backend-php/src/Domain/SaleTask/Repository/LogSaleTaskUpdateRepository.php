<?php

namespace App\Domain\SaleTask\Repository;

use App\Domain\SaleTask\Data\SaleTaskData;
use PDO;

final class LogSaleTaskUpdateRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function logTaskState(SaleTaskData $data): bool
    {
        $query = "INSERT INTO sale_task_log (
                      user_id,
                      sale_task_id,
                      task_id,
                      task_no,
                      task_status,
                      task_payment,
                      task_payment_type,
                      task_days,
                      task_frequency,
                      task_completion_date,
                      comments,
                      created,
                      modified
                  )
                  SELECT
                      :user_id,
                      st.sale_task_id,
                      st.task_id,
                      st.task_no,
                      st.task_status,
                      st.task_payment,
                      st.task_payment_type,
                      st.task_days,
                      st.task_frequency,
                      st.task_completion_date,
                      :comments,
                      CURRENT_TIMESTAMP,
                      CURRENT_TIMESTAMP
                  FROM sale_task st
                  WHERE st.sale_task_id = :sale_task_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'user_id' => $data->user_id,
            'sale_task_id' => $data->sale_task_id,
            'comments' => $data->comments
        ];

        return $query_stmt->execute($query_data);
    }
}
