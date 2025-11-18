<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDO;

final class UpdateEscalationMonitorRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateMonitor(TaskMonitorData $data): bool
    {
        $query = "UPDATE escalation_monitor SET 
                      escalation_type = :escalation_type,
                      escalation_days = :escalation_days 
                  WHERE 
                      escalation_id = :escalation_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'escalation_id' => $data->escalation_id,
            'escalation_type' => $data->escalation_type->value,
            'escalation_days' => $data->escalation_days
        ];

        return $query_stmt->execute($query_data);
    }
}
