<?php

namespace App\Domain\TaskMonitor\Service;

use App\Util\Utilities;

final class SanitizeTaskMonitorDataService
{
    public static function sanitize(array $data): array
    {
        $tasks = [];
        if (isset($data['tasks']) && !empty($data['tasks'])) {
            if (is_array($data['tasks'])) {
                foreach ($data['tasks'] as $task) {
                    if (empty($task)) {
                        continue;
                    }
                    $tasks[] = Utilities::sanitizeString($task);
                }
            } else {
                $tasks[] = Utilities::sanitizeString($data['tasks']);
            }
        }

        return [
            'username' => Utilities::sanitizeString($data['username'] ?? ''),
            'escalationId' => Utilities::sanitizeString($data['escalationId'] ?? ''),
            'escalationTaskId' => Utilities::sanitizeString($data['escalationTaskId'] ?? ''),
            'escalationType' => Utilities::sanitizeString($data['escalationType'] ?? ''),
            'escalationDays' => intval(Utilities::sanitizeString($data['escalationDays'] ?? '')),
            'taskId' => Utilities::sanitizeString($data['taskId'] ?? ''),
            'tasks' => $tasks,
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
