<?php

namespace App\Domain\Task\Service\UpdateTask;

use App\Domain\Task\Repository\TaskNameExistsRepository;
use App\Domain\Task\Service\GetTaskService;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateTaskDataService
{
    private TaskNameExistsRepository $taskNameExistsRepository;
    private GetTaskService $getTaskService;

    public function __construct(PDO $connection)
    {
        $this->taskNameExistsRepository = new TaskNameExistsRepository($connection);
        $this->getTaskService = new GetTaskService($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $tasks = $this->getTaskService->getTask([
            'accountNo' => $data['accountNo'],
            'taskId' => $data['taskId']
        ]);

        if (empty($data['taskId']) || count($tasks['records']) !== 1) {
            throw new ValidationException('Invalid or missing task details');
        }

        if (empty($data['taskName'])) {
            throw new ValidationException('Data validation error', 422, [
                'taskName' => 'Invalid task name provided'
            ]);
        }

        foreach ($tasks['records'] as $task) {
            $nameChange = trim(strtolower($data['taskName'])) !== trim(strtolower($task['taskName']));
            if ($nameChange && $this->taskNameExistsRepository->nameExists($data['accountNo'], $data['taskName'])) {
                throw new ValidationException('Data validation error', 422, [
                    'taskName' => sprintf('%s: is already is use', $data['taskName'])
                ]);
            }
        }

        return $data;
    }
}
