<?php

namespace App\Domain\Task\Service\UpdateTask;

use App\Domain\Task\Repository\UpdateTaskRepository;
use App\Domain\Task\Service\SanitizeTaskDataService;
use App\Domain\Task\Service\MapTaskDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateTaskService
{
    private UpdateTaskRepository $updateTaskRepository;
    private ValidateUpdateTaskDataService $validateUpdateTaskDataService;

    public function __construct(PDO $connection)
    {
        $this->updateTaskRepository = new UpdateTaskRepository($connection);
        $this->validateUpdateTaskDataService = new ValidateUpdateTaskDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateTask(array $data): array
    {
        $sanitizedData = SanitizeTaskDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateUpdateTaskDataService->validateData($sanitizedData);

        $taskData = MapTaskDataService::map($validatedData);
        if ($this->updateTaskRepository->updateTask($taskData)) {
            return [
                'success' => 'Task details updated',
                'id' => $taskData->task_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
