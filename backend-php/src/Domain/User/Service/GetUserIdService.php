<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetUserIdService
{
    private UserIdExistsRepository $userIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->userIdExistsRepository = new UserIdExistsRepository($connection);
    }

    public function getUniqueId($length = 32): string
    {
        do {
            $user_id = Utilities::generateToken($length);
        } while(empty($user_id) || $this->userIdExistsRepository->userIdExists($user_id));

        return $user_id;
    }
}
