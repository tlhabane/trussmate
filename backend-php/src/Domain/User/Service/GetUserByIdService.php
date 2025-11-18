<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\GetUserByIdRepository;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetUserByIdService
{
    private GetUserByIdRepository $getUserByIdRepository;

    public function __construct(PDO $connection)
    {
        $this->getUserByIdRepository = new GetUserByIdRepository($connection);
    }

    public function getUser(string $username) {
        $sanitizedUsername = Utilities::sanitizeString($username);
        $users = $this->getUserByIdRepository->getUser($sanitizedUsername);

        $records = [];
        foreach ($users as $user) {
            $records[] = [
                'accountNo' => $user['account_no'],
                'username' => $user['user_id'],
                'regionId' => $user['region_id'],
                'regionName' => Utilities::decodeUTF8($user['region_name']),
                'userStatus' => $user['user_status'],
                'userHash' => $user['user_hash'],
                'userRole' => $user['user_role'],
                'firstName' => Utilities::decodeUTF8($user['first_name']),
                'lastName' => Utilities::decodeUTF8($user['last_name']),
                'jobTitle' => Utilities::decodeUTF8($user['job_title']),
                'tel' => $user['tel'],
                'altTel' => $user['alt_tel'],
                'email' => $user['email']
            ];
        }

        return ['records' => $records];
    }
}
