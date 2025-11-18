<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\GetUserRepository;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetUserService
{
    private GetUserRepository $getUserRepository;

    public function __construct(PDO $connection)
    {
        $this->getUserRepository = new GetUserRepository($connection);
    }

    public function getUser(array $data): array
    {
        $sanitizedData = SanitizeUserDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset($sanitizedData['page'], $sanitizedData['recordsPerPage']);
        $userData = MapUserDataService::mapData($sanitizedData);
        $userData->account_no = $data['accountNo'];
        $userRole = GetUserRoleService::getUserRole($data['sessionUserRole']);
        if ($userRole->value > 2) {
            $userData->username = $data['sessionUsername'];
        }

        $records = [];
        $users = $this->getUserRepository->getUser(
            $userData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );
        foreach ($users as $user) {
            $records[] = [
                'username' => $user['user_id'],
                'regionId' => $user['region_id'],
                'regionName' => Utilities::decodeUTF8($user['region_name']),
                'userStatus' => $user['user_status'],
                'userRole' => $user['user_role'],
                'firstName' => Utilities::decodeUTF8($user['first_name']),
                'lastName' => Utilities::decodeUTF8($user['last_name']),
                'jobTitle' => Utilities::decodeUTF8($user['job_title']),
                'tel' => $user['tel'],
                'altTel' => $user['alt_tel'],
                'email' => $user['email']
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getUserRepository->getUser($userData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }
}
