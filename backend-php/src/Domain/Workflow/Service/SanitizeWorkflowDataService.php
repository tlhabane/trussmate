<?php

namespace App\Domain\Workflow\Service;

use App\Util\Utilities;

final class SanitizeWorkflowDataService
{
    public static function sanitizeData(array $data): array
    {
        $tasks = [];
        if (isset($data['tasks']) && is_array($data['tasks'])) {
            foreach ($data['tasks'] as $task) {
                $task_id = Utilities::sanitizeString($task['taskId'] ?? '');
                if (empty($task_id)) {
                    continue;
                }
                $tasks[] = [
                    'workflowTaskId' => Utilities::sanitizeString($task['workflowTaskId'] ?? ''),
                    'taskId' => $task_id,
                    'taskNo' => intval(Utilities::sanitizeString($task['taskNo'] ?? '')),
                    'triggerType' => Utilities::sanitizeString($task['triggerType'] ?? ''),
                    'taskOptional' => intval(Utilities::sanitizeString($task['taskOptional'] ?? '')),
                    'assignedTo' => Utilities::sanitizeString($task['assignedTo'] ?? ''),
                    'assignmentNote' => Utilities::sanitizeAndEncodeString($task['assignmentNote'] ?? ''),
                ];
            }
        }

        return [
            'workflowId' => Utilities::sanitizeString($data['workflowId'] ?? ''),
            'workflowName' => Utilities::sanitizeAndEncodeString($data['workflowName'] ?? ''),
            'tasks' => $tasks,
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
