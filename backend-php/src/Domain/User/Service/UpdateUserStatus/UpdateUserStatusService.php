<?php

namespace App\Domain\User\Service\UpdateUserStatus;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\User\Repository\UpdateUserAccountStatusRepository;
use App\Domain\User\Service\SanitizeUserDataService;
use App\Domain\User\Service\MapUserDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateUserStatusService
{
    private UpdateUserAccountStatusRepository $updateUserAccountStatusRepository;
    private ValidateUpdateUserStatusDataService $validateUpdateUserStatusDataService;

    public function __construct(PDO $connection)
    {
        $this->updateUserAccountStatusRepository = new UpdateUserAccountStatusRepository($connection);
        $this->validateUpdateUserStatusDataService = new ValidateUpdateUserStatusDataService($connection);
    }

    /**
     * @throws RuntimeException
     * @throws ValidationException
     */
    public function updateStatus(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $sanitizedData = SanitizeUserDataService::sanitizeData($data);
        $validatedData = $this->validateUpdateUserStatusDataService->validateData($sanitizedData);

        $userData = MapUserDataService::mapData($validatedData);
        if ($this->updateUserAccountStatusRepository->updateStatus($userData)) {
            return [
                'success' => sprintf('User account %s', $userData->user_status->name),
                'id' => $userData->username
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
