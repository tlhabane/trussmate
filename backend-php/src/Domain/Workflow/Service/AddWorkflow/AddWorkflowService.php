<?php

namespace App\Domain\Workflow\Service\AddWorkflow;

use App\Domain\Workflow\Repository\AddWorkflowRepository;
use App\Domain\Workflow\Repository\AddWorkflowTaskRepository;
use App\Domain\Workflow\Service\GetWorkflowTaskIdService;
use App\Domain\Workflow\Repository\DeleteWorkflowRepository;
use App\Domain\Workflow\Service\SanitizeWorkflowDataService;
use App\Domain\Workflow\Service\MapWorkflowTaskDataService;
use App\Domain\Workflow\Service\MapWorkflowDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class AddWorkflowService
{
    private AddWorkflowRepository $addWorkflowRepository;
    private AddWorkflowTaskRepository $addWorkflowTaskRepository;
    private DeleteWorkflowRepository $deleteWorkflowRepository;
    private GetWorkflowIdService $getWorkflowIdService;
    private GetWorkflowTaskIdService $getWorkflowTaskIdService;
    private ValidateAddWorkflowDataService $validateAddWorkflowDataService;

    public function __construct(PDO $connection)
    {
        $this->addWorkflowRepository = new AddWorkflowRepository($connection);
        $this->addWorkflowTaskRepository = new AddWorkflowTaskRepository($connection);
        $this->deleteWorkflowRepository = new DeleteWorkflowRepository($connection);
        $this->getWorkflowIdService = new GetWorkflowIdService($connection);
        $this->getWorkflowTaskIdService = new GetWorkflowTaskIdService($connection);
        $this->validateAddWorkflowDataService = new ValidateAddWorkflowDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addWorkflow(array $data): array
    {
        $sanitizedData = SanitizeWorkflowDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateAddWorkflowDataService->validateData($sanitizedData);

        $workflowData = MapWorkflowDataService::map($validatedData);
        $workflowData->workflow_id = $this->getWorkflowIdService->getId(128);
        $workflowData->account_no = $data['accountNo'];

        try {
            if ($this->addWorkflowRepository->addWorkflow($workflowData)) {
                foreach ($sanitizedData['tasks'] as $task) {
                    $taskData = MapWorkflowTaskDataService::map($task);
                    $taskData->workflow_id = $workflowData->workflow_id;
                    $taskData->workflow_task_id = $this->getWorkflowTaskIdService->getId(64);
                    $this->addWorkflowTaskRepository->addTask($taskData);
                }

                return [
                    'success' => 'Workflow details saved',
                    'id' => $workflowData->workflow_id
                ];
            }
        } catch (Exception $e) {
            // If an error occurs, delete the workflow to maintain data integrity
            $this->deleteWorkflowRepository->deleteWorkflow($workflowData->workflow_id);
            throw new RuntimeException($e->getMessage());
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing your request, please try again.'
        );
    }
}
