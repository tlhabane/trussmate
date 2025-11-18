<?php

namespace App\Domain\User\Service\UpdateUserStatus;

use App\Domain\User\Service\GetUserStatusService;
use App\Domain\User\Repository\UserIdExistsRepository;
use App\Contract\DataValidationContract;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateUserStatusDataService implements DataValidationContract
{
    private UserIdExistsRepository $userIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->userIdExistsRepository = new UserIdExistsRepository($connection);
    }

    public function validateData(array $data): array
    {
        if (empty($data['username']) || !$this->userIdExistsRepository->userIdExists($data['username'])) {
            throw new ValidationException('Invalid or missing user details');
        }

        $userStatus = GetUserStatusService::getStatus($data['userStatus'] ?? '');
        if ($userStatus->value === 0) {
            throw new ValidationException('Data validation error', 422, [
                'userStatus' => 'Invalid user account status provided'
            ]);
        }

        return $data;
    }
}
