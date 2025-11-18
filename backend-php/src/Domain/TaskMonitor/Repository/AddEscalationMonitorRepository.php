<?php

namespace App\Domain\TaskMonitor\Repository;

use App\Domain\TaskMonitor\Data\TaskMonitorData;
use PDO;

final class AddEscalationMonitorRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addMonitor(TaskMonitorData $data): bool
    {
        $query = "INSERT INTO escalation_monitor SET 
                  account_no = :account_no,
                  escalation_id = :escalation_id,
                  escalation_type = :escalation_type,
                  escalation_days = :escalation_days";

        $query_stmt = $this->connection->prepare($query);
        $query_data = [
            'account_no' => $data->account_no,
            'escalation_id' => $data->escalation_id,
            'escalation_type' => $data->escalation_type->value,
            'escalation_days' => $data->escalation_days
        ];

        return $query_stmt->execute($query_data);
    }
}
