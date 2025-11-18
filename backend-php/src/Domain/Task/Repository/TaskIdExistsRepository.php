<?php

namespace App\Domain\Task\Repository;

use PDO;

final class TaskIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $task_id): bool
    {
        $query = 'SELECT task_id FROM task WHERE task_id = :task_id';
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':task_id', $task_id);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
