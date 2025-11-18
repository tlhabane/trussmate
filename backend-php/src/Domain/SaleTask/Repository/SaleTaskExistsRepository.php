<?php

namespace App\Domain\SaleTask\Repository;

use App\Domain\SaleTask\Data\SaleTaskData;
use PDO;

final class SaleTaskExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function taskExists(SaleTaskData $data): bool
    {
        $query = "SELECT task_id FROM sale_task WHERE sale_id = :sale_id AND task_id = :task_id";
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':sale_id', $data->sale_id);
        $query_stmt->bindParam(':task_id', $data->task_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
