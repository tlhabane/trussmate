<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\TaskMonitor\Repository\EscalationTaskIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetEscalationTaskIdService
{
    private EscalationTaskIdExistsRepository $escalationTaskIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->escalationTaskIdExistsRepository = new EscalationTaskIdExistsRepository($connection);
    }

    public function getId(int $length = 64): string
    {
        do {
            $escalation_task_id = Utilities::generateToken($length);
        } while (empty($escalation_task_id) || $this->escalationTaskIdExistsRepository->idExists($escalation_task_id));

        return $escalation_task_id;
    }
}
