<?php

namespace App\Domain\Workflow\Service\AddWorkflow;

use App\Domain\Workflow\Repository\WorkflowIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetWorkflowIdService
{
    private WorkflowIdExistsRepository $workflowIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->workflowIdExistsRepository = new WorkflowIdExistsRepository($connection);
    }

    public function getId(int $length): string
    {
        do {
            $workflow_id = Utilities::generateToken($length);
        } while (empty($workflow_id) || $this->workflowIdExistsRepository->idExists($workflow_id));

        return $workflow_id;
    }
}
