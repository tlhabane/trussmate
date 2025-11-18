<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Repository\GetTaskRepository;
use App\Util\DataPagination;
use PDO;

final class GetTaskService
{
    private GetTaskRepository $getTaskRepository;

    public function __construct(PDO $connection)
    {
        $this->getTaskRepository = new GetTaskRepository($connection);
    }

    public function getTask(array $data): array
    {
        $sanitizedData = SanitizeTaskDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $taskData = MapTaskDataService::map($sanitizedData);
        $taskData->account_no = $data['accountNo'];

        $tasks = $this->getTaskRepository->getTask(
            $taskData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        $records = GetFormattedTasksService::getTasks($tasks);

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getTaskRepository->getTask($taskData);
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
