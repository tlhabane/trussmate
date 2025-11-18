<?php

namespace App\Domain\Task\Repository;

use PDO;

final class TaskNameExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function nameExists(string $account_no, string $task_name): bool
    {
        $query = "SELECT 
                      task_name 
                  FROM 
                      task   
                  WHERE 
                      account_no = :account_no AND 
                      TRIM(LOWER(task_name)) = :task_name";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $account_no);
        $formatted_task_name = trim(strtolower($task_name));
        $query_stmt->bindParam(':task_name', $formatted_task_name);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
