<?php

namespace App\Domain\TaskMonitor\Repository;

use PDO;

final class EscalationIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $escalation_id): bool
    {
        $query = "SELECT escalation_id FROM escalation_monitor WHERE escalation_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $escalation_id);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
