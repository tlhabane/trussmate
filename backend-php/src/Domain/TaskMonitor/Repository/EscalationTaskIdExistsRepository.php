<?php

namespace App\Domain\TaskMonitor\Repository;

use PDO;

final class EscalationTaskIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $escalation_task_id): bool
    {
        $query = "SELECT escalation_task_id FROM escalation_task WHERE escalation_task_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $escalation_task_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
