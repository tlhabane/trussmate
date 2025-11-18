<?php

namespace App\Domain\User\Service\UpdateUserRole;

use App\Contract\DataValidationContract;
use App\Domain\User\Service\GetUserRoleService;
use App\Domain\User\Repository\UserIdExistsRepository;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateUserRoleDataService implements DataValidationContract
{
    private UserIdExistsRepository $userIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->userIdExistsRepository = new UserIdExistsRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        if (empty($data['username']) || !$this->userIdExistsRepository->userIdExists($data['username'])) {
            throw new ValidationException('Invalid or missing user details');
        }

        $userRole = GetUserRoleService::getUserRole($data['userRole'] ?? '');
        if ($userRole->value === 0) {
            throw new ValidationException('Invalid user role provided');
        }

        return $data;
    }
}
