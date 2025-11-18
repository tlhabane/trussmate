<?php

namespace App\Domain\Workflow\Service\UpdateWorkflow;

use App\Domain\Workflow\Repository\GetWorkflowTaskRepository;
use App\Domain\Workflow\Repository\DeleteAllWorkflowTaskRepository;
use App\Domain\Workflow\Repository\UpdateWorkflowRepository;
use App\Domain\Workflow\Repository\AddWorkflowTaskRepository;
use App\Domain\Workflow\Service\GetWorkflowTaskIdService;
use App\Domain\Workflow\Service\SanitizeWorkflowDataService;
use App\Domain\Workflow\Service\MapWorkflowTaskDataService;
use App\Domain\Workflow\Service\MapWorkflowDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class UpdateWorkflowService
{
    private GetWorkflowTaskRepository $getWorkflowTaskRepository;
    private DeleteAllWorkflowTaskRepository $deleteAllWorkflowTaskRepository;
    private UpdateWorkflowRepository $updateWorkflowRepository;
    private AddWorkflowTaskRepository $addWorkflowTaskRepository;
    private GetWorkflowTaskIdService $getWorkflowTaskIdService;
    private ValidateUpdateWorkflowDataService $validateUpdateWorkflowDataService;

    public function __construct(PDO $connection)
    {
        $this->getWorkflowTaskRepository = new GetWorkflowTaskRepository($connection);
        $this->deleteAllWorkflowTaskRepository = new DeleteAllWorkflowTaskRepository($connection);
        $this->updateWorkflowRepository = new UpdateWorkflowRepository($connection);
        $this->addWorkflowTaskRepository = new AddWorkflowTaskRepository($connection);
        $this->getWorkflowTaskIdService = new GetWorkflowTaskIdService($connection);
        $this->validateUpdateWorkflowDataService = new ValidateUpdateWorkflowDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateWorkflow(array $data): array
    {
        $sanitizedData = SanitizeWorkflowDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateUpdateWorkflowDataService->validateData($sanitizedData);

        $workflowData = MapWorkflowDataService::map($validatedData);
        $currentTasks = $this->getWorkflowTaskRepository->getTask($workflowData->workflow_id);
        $this->deleteAllWorkflowTaskRepository->deleteTasks($workflowData->workflow_id);

        try {
            if ($this->updateWorkflowRepository->updateWorkflow($workflowData)) {
                foreach ($sanitizedData['tasks'] as $task) {
                    $taskData = MapWorkflowTaskDataService::map($task);
                    $taskData->workflow_id = $workflowData->workflow_id;
                    $taskData->workflow_task_id = $this->getWorkflowTaskIdService->getId(64);
                    $this->addWorkflowTaskRepository->addTask($taskData);
                }

                return [
                    'success' => 'Workflow details updated',
                    'id' => $workflowData->workflow_id
                ];
            }
        } catch (Exception $e) {
            // If an error occurs, restore previous tasks
            foreach ($currentTasks as $task) {
                $rawTaskData = [
                    'workflowTaskId' => $task['workflow_task_id'],
                    'taskId' => $task['task_id'],
                    'taskNo' => $task['task_no'],
                    'triggerType' => $task['trigger_type'],
                    'taskOptional' => $task['task_optional'] ? 1 : 0,
                    'assignedTo' => $task['assigned_to'],
                    'assignmentNote' => $task['assignment_note']
                ];
                $taskData = MapWorkflowTaskDataService::map($rawTaskData);
                $taskData->workflow_id = $workflowData->workflow_id;
                $this->addWorkflowTaskRepository->addTask($taskData);
            }
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing your request, please try again.'
        );
    }
}
