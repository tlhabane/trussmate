<?php

namespace App\Domain\Workflow\Service;

use App\Domain\Workflow\Repository\WorkflowAlreadyInUseRepository;
use App\Domain\Workflow\Repository\DeleteWorkflowRepository;
use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class DeleteWorkflowService
{
    private WorkflowAlreadyInUseRepository $workflowAlreadyInUseRepository;
    private DeleteWorkflowRepository $deleteWorkflowRepository;

    public function __construct(PDO $connection)
    {
        $this->workflowAlreadyInUseRepository = new WorkflowAlreadyInUseRepository($connection);
        $this->deleteWorkflowRepository = new DeleteWorkflowRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function deleteWorkflow(array $data): array
    {
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        
        $sanitizedData = SanitizeWorkflowDataService::sanitizeData($data);

        if ($this->workflowAlreadyInUseRepository->workflowInUse($sanitizedData['workflowId'])) {
            throw new ValidationException('Selected workflow is already in use');
        }

        if ($this->deleteWorkflowRepository->deleteWorkflow($sanitizedData['workflowId'])) {
            return [
                'success' => 'Sales workflow deleted',
                'id' => $sanitizedData['workflowId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing your request, please try again.'
        );
    }
}
