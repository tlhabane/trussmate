<?php

namespace App\Domain\Workflow\Service\UpdateWorkflow;

use App\Domain\Workflow\Service\GetWorkflowService;
use App\Domain\Workflow\Repository\WorkflowNameExistsRepository;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateWorkflowDataService
{
    private GetWorkflowService $getWorkflowService;
    private WorkflowNameExistsRepository $workflowNameExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->getWorkflowService = new GetWorkflowService($connection);
        $this->workflowNameExistsRepository = new WorkflowNameExistsRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $workflows = $this->getWorkflowService->getWorkflow([
            'accountNo' => $data['accountNo'],
            'workflowId' => $data['workflowId']
        ]);

        if (empty($data['workflowId']) && count($workflows['records']) !== 1) {
            throw new ValidationException('Invalid or missing workflow details');
        }

        if (empty($data['tasks']) || !is_array($data['tasks'])) {
            throw new ValidationException('At least 1(one) workflow task is required.');
        }

        $fields = [];
        foreach ($workflows['records'] as $workflow) {
            if (empty($data['workflowName'])) {
                $fields['workflowName'] = 'Workflow name is required.';
            }
            $nameUpdate = $data['workflowName'] !== $workflow['workflowName'];
            $nameExists = $this->workflowNameExistsRepository->nameExists($data['accountNo'], $data['workflowName']);
            if ($nameUpdate && $nameExists) {
                $fields['workflowName'] = sprintf('%s: Workflow name is already in use.', $data['workflowName']);
            }
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
