<?php

namespace App\Domain\User\Service;

use App\Domain\User\Data\UserRole;
use App\Domain\User\Data\UserStatus;
use App\Domain\User\Repository\AddUserAccountRepository;
use App\Domain\User\Repository\DeleteUserRepository;
use App\Domain\User\Service\AddUserAccountInfo\AddUserAccountInfoService;
use App\Domain\User\Service\UpdateUserHash\ValidateUpdateUserHashDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddSuperAdminUserService
{
    private GetUserIdService $getUserIdService;
    private AddUserAccountRepository $addUserAccountRepository;
    private DeleteUserRepository $deleteUserRepository;
    private AddUserAccountInfoService $addUserAccountInfoService;
    private ValidateUpdateUserHashDataService $validateUpdateUserHashDataService;

    public function __construct(PDO $connection)
    {
        $this->getUserIdService = new GetUserIdService($connection);
        $this->addUserAccountRepository = new AddUserAccountRepository($connection);
        $this->deleteUserRepository = new DeleteUserRepository($connection);
        $this->addUserAccountInfoService = new AddUserAccountInfoService($connection);
        $this->validateUpdateUserHashDataService = new ValidateUpdateUserHashDataService();
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addUser(array $data): array
    {
        $serviceData = array_merge($data, [
            'username' => $this->getUserIdService->getUniqueId(64),
            'userRole' => UserRole::super_admin->name,
            'userStatus' => UserStatus::active->name
        ]);
        $sanitizedData = SanitizeUserDataService::sanitizeData($serviceData);
        $validatedData = $this->validateUpdateUserHashDataService->validateData($sanitizedData);

        try {
            /* Add user account info */
            $userInfo = array_merge($validatedData, [
                'sessionUsername' => $serviceData['username'],
                'sessionUserRole' => UserRole::super_admin->name,
            ]);
            $this->addUserAccountInfoService->addUserInfo($userInfo);
            /* Add user account */
            $userData = MapUserDataService::mapData($validatedData);
            /* Re-inject accountNo & hash lost during sanitation */
            $userData->account_no = $data['accountNo'];
            $userData->user_hash = $validatedData['userHash'];
            if ($this->addUserAccountRepository->addAccount($userData)) {
                return [
                    'success' => 'New user added',
                    'id' => $userData->username
                ];
            }
        } catch (RuntimeException|ValidationException $exception) {
            /* Rollback user changes */
            $this->deleteUserRepository->deleteUser($serviceData['username']);
            /* Rethrow exception */
            if ($exception instanceof ValidationException) {
                throw new ValidationException($exception->getMessage(), $exception->getCode(), $exception->getErrors());
            }

            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
