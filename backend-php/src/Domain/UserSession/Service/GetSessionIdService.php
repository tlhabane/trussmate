<?php

namespace App\Domain\UserSession\Service;

use App\Domain\UserSession\Repository\SessionIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetSessionIdService
{
    private SessionIdExistsRepository $sessionIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->sessionIdExistsRepository = new SessionIdExistsRepository($connection);
    }

    public function getUniqueId(): string
    {
        do {
            $sessionId = Utilities::generateToken();
        } while (empty($sessionId) || $this->sessionIdExistsRepository->sessionIdExists($sessionId));

        return $sessionId;
    }
}
