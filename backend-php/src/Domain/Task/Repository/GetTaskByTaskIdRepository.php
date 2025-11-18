<?php

namespace App\Domain\Task\Repository;

use PDOStatement;
use PDO;

final class GetTaskByTaskIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTask(string $task_id): PDOStatement
    {
        $query = "SELECT 
                      task_id, task_name, task_description, task_payment, task_payment_type, task_days, task_frequency, 
                      task_document, task_action 
                  FROM 
                      task 
                  WHERE 
                      task_id = :task_id";


        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':task_id', $task_id);

        $query_stmt->execute();
        return $query_stmt;
    }
}
