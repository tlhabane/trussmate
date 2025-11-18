<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Repository\GetTaskByTaskIdRepository;
use PDO;

final class GetTaskByTaskIdService
{
    private GetTaskByTaskIdRepository $getTaskByTaskIdRepository;

    public function __construct(PDO $connection)
    {
        $this->getTaskByTaskIdRepository = new GetTaskByTaskIdRepository($connection);
    }

    public function getTask(array $data): array
    {
        $sanitizedData = SanitizeTaskDataService::sanitizeData($data);
        $tasks = $this->getTaskByTaskIdRepository->getTask($sanitizedData['taskId']);

        $records = GetFormattedTasksService::getTasks($tasks);

        return ['records' => $records];
    }
}
