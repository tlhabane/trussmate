<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\TaskMonitor\Repository\GetEscalationTaskRepository;
use App\Exception\RuntimeException;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetEscalationTaskMonitorService
{
    private GetEscalationTaskRepository $getEscalationTaskRepository;

    public function __construct(PDO $connection)
    {
        $this->getEscalationTaskRepository = new GetEscalationTaskRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function getEscalationTask(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeTaskMonitorDataService::sanitize($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $taskMonitorData = SetTaskMonitorDataService::set($sanitizedData);
        $taskMonitorData->account_no = $data['accountNo'];

        $records = [];
        $tasks = $this->getEscalationTaskRepository->getTask(
            $taskMonitorData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );
        foreach ($tasks as $task) {
            $records[] = [
                'taskId' => $task['task_id'],
                'taskDays' => intval($task['task_days']),
                'taskName' => Utilities::decodeUTF8($task['task_name']),
                'taskDescription' => Utilities::decodeUTF8($task['task_description']),
                'escalationId' => $task['escalation_id'],
                'escalationType' => $task['escalation_type'],
                'escalationDays' => intval($task['escalation_days']),
                'firstName' => Utilities::decodeUTF8($task['first_name']),
                'lastName' => Utilities::decodeUTF8($task['last_name']),
                'email' => $task['email'],
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getEscalationTaskRepository->getTask($taskMonitorData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }
}
