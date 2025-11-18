<?php

namespace App\Domain\Task\Repository;

use PDO;

final class DeleteTaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $task_id): bool
    {
        $query = 'DELETE FROM task WHERE task_id = :task_id';
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':task_id', $task_id);
        return $query_stmt->execute();;
    }
}
