<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\TaskMonitor\Repository\EscalationIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetEscalationIdService
{
    private EscalationIdExistsRepository $escalationIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->escalationIdExistsRepository = new EscalationIdExistsRepository($connection);
    }

    public function getId(int $length = 64): string
    {
        do {
            $escalation_id = Utilities::generateToken($length);
        } while (empty($escalation_id) || $this->escalationIdExistsRepository->idExists($escalation_id));

        return $escalation_id;
    }
}
