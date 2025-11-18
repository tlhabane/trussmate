<?php

namespace App\Domain\Workflow\Service\AddWorkflow;

use App\Domain\Workflow\Repository\WorkflowNameExistsRepository;
use App\Exception\ValidationException;
use PDO;

final class ValidateAddWorkflowDataService
{
    private WorkflowNameExistsRepository $workflowNameExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->workflowNameExistsRepository = new WorkflowNameExistsRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        if (empty($data['workflowName'])) {
            throw new ValidationException('Data validation error', 422, [
                'workflowName' => 'Workflow name is required.',
            ]);
        }

        if ($this->workflowNameExistsRepository->nameExists($data['accountNo'], $data['workflowName'])) {
            throw new ValidationException('Data validation error', 422, [
                'workflowName' => sprintf('%s: Workflow name is required.', $data['workflowName'])
            ]);
        }

        if (empty($data['tasks']) || !is_array($data['tasks'])) {
            throw new ValidationException('At least 1(one) workflow task is required.');
        }

        return $data;
    }
}
