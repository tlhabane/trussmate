<?php

namespace App\Domain\UserSession\Service;

use App\Domain\UserSession\Repository\EndAllUserSessionsRepository;
use App\Util\Utilities;
use PDO;

final class EndAllUserSessionsService
{
    private EndAllUserSessionsRepository $endAllUserSessionsRepository;

    public function __construct(PDO $connection) {
        $this->endAllUserSessionsRepository = new EndAllUserSessionsRepository($connection);
    }

    public function endSession(string $user_id): bool
    {
        $sanitizedUserId = Utilities::sanitizeString($user_id);
        return $this->endAllUserSessionsRepository->endSessions($sanitizedUserId);
    }
}
