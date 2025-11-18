<?php

namespace App\Domain\User\Service\UpdateUserAccountInfo;

use App\Domain\User\Repository\UpdateUserAccountInfoRepository;
use App\Domain\User\Service\SanitizeUserDataService;
use App\Domain\User\Service\MapUserDataService;
use App\Domain\User\Service\GetUserRoleService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateUserAccountInfoService
{
    private UpdateUserAccountInfoRepository $updateUserAccountInfoRepository;
    private ValidateUpdateUserAccountInfoService $validateUpdateUserAccountInfoService;

    public function __construct(PDO $connection)
    {
        $this->updateUserAccountInfoRepository = new UpdateUserAccountInfoRepository($connection);
        $this->validateUpdateUserAccountInfoService = new ValidateUpdateUserAccountInfoService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateAccountInfo(array $data): array
    {
        $sanitizedData = SanitizeUserDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateUpdateUserAccountInfoService->validateData($sanitizedData);

        $userData = MapUserDataService::mapData($validatedData);
        if ($this->updateUserAccountInfoRepository->updateInfo($userData)) {
            $profileUpdate = $userData->username = $data['userId'];
            $suffix = 'info updated';
            return [
                'success' => $profileUpdate ? "Account {$suffix}" : "User account {$suffix}",
                'id' => $userData->username
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
