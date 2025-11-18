<?php

namespace App\Domain\User\Service\UpdateUserRole;

use App\Domain\User\Repository\UpdateUserRoleRepository;
use App\Domain\User\Service\MapUserDataService;
use App\Domain\User\Service\SanitizeUserDataService;
use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateUserRoleService
{
    private UpdateUserRoleRepository $updateUserRoleRepository;
    private ValidateUpdateUserRoleDataService $validateUpdateUserRoleDataService;

    public function __construct(PDO $connection)
    {
        $this->updateUserRoleRepository = new UpdateUserRoleRepository($connection);
        $this->validateUpdateUserRoleDataService = new ValidateUpdateUserRoleDataService($connection);
    }

    /**
     * @throws RuntimeException
     * @throws ValidationException
     */
    public function updateUserRole(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $sanitizedData = SanitizeUserDataService::sanitizeData($data);
        $validatedData = $this->validateUpdateUserRoleDataService->validateData($sanitizedData);
        $userData = MapUserDataService::mapData($validatedData);

        if ($this->updateUserRoleRepository->updateRole($userData)) {
            return [
                'success' => 'User role updated',
                'id' => $userData->username
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
