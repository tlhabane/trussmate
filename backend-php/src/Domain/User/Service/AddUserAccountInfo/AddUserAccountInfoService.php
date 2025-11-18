<?php

namespace App\Domain\User\Service\AddUserAccountInfo;

use App\Domain\User\Repository\AddUserAccountInfoRepository;
use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\User\Service\SanitizeUserDataService;
use App\Domain\User\Service\MapUserDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddUserAccountInfoService
{
    private AddUserAccountInfoRepository $addUserAccountInfoRepository;
    private ValidateAddUserInfoAccountInfoService $validateAddUserInfoAccountInfoService;

    public function __construct(PDO $connection)
    {
        $this->addUserAccountInfoRepository = new AddUserAccountInfoRepository($connection);
        $this->validateAddUserInfoAccountInfoService = new ValidateAddUserInfoAccountInfoService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addUserInfo(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $sanitizedData = SanitizeUserDataService::sanitizeData($data);
        $validatedData = $this->validateAddUserInfoAccountInfoService->validateData($sanitizedData);

        $userData = MapUserDataService::mapData($validatedData);
        if ($this->addUserAccountInfoRepository->addInfo($userData)) {
            return [
                'success' => 'User account info added',
                'id' => $userData->username
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
