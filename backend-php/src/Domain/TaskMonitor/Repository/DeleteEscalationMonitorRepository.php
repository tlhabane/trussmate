<?php

namespace App\Domain\TaskMonitor\Repository;

use PDO;

final class DeleteEscalationMonitorRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteTask(string $escalation_id): bool
    {
        $query = "DELETE
                      em, en, et 
                  FROM 
                      escalation_monitor em 
                  LEFT JOIN
                      escalation_notification en ON em.escalation_id = en.escalation_id
                  LEFT JOIN
                      escalation_task et ON em.escalation_id = et.escalation_id
                  WHERE 
                      em.escalation_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $escalation_id);
        return $query_stmt->execute();
    }
}
