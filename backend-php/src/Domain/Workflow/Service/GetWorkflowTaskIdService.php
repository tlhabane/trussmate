<?php

namespace App\Domain\Workflow\Service;

use App\Domain\Workflow\Repository\WorkflowTaskIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetWorkflowTaskIdService
{
    private WorkflowTaskIdExistsRepository $workflowTaskIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->workflowTaskIdExistsRepository = new WorkflowTaskIdExistsRepository($connection);
    }

    public function getId(int $length): string
    {
        do {
            $task_id = Utilities::generateToken($length);
        } while (empty($task_id) || $this->workflowTaskIdExistsRepository->idExists($task_id));

        return $task_id;
    }
}
