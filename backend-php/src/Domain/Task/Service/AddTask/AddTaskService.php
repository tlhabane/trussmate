<?php

namespace App\Domain\Task\Service\AddTask;

use App\Domain\Task\Repository\AddTaskRepository;
use App\Domain\Task\Service\SanitizeTaskDataService;
use App\Domain\Task\Service\MapTaskDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddTaskService
{
    private AddTaskRepository $addTaskRepository;
    private GetTaskIdService $getTaskIdService;

    public function __construct(PDO $connection)
    {
        $this->addTaskRepository = new AddTaskRepository($connection);
        $this->getTaskIdService = new GetTaskIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addTask(array $data): array
    {
        $sanitizedData = SanitizeTaskDataService::sanitizeData($data);
        $validatedData = ValidateAddTaskDataService::validateData($sanitizedData);

        $taskData = MapTaskDataService::map($validatedData);
        $taskData->task_id = $this->getTaskIdService->getId(64);
        $taskData->account_no = $data['accountNo'];
        if ($this->addTaskRepository->addTask($taskData)) {
            return [
                'success' => 'Task details saved',
                'id' => $taskData->task_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
