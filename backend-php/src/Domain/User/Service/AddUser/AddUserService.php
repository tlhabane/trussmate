<?php

namespace App\Domain\User\Service\AddUser;

use App\Domain\User\Data\UserStatus;
use App\Domain\User\Repository\AddUserAccountRepository;
use App\Domain\User\Repository\AddUserInvitationRepository;
use App\Domain\User\Repository\DeleteUserRepository;
use App\Domain\User\Service\AddUserAccountInfo\AddUserAccountInfoService;
use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\User\Service\GetUserIdService;
use App\Domain\User\Service\SanitizeUserDataService;
use App\Domain\User\Service\MapUserDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddUserService
{
    private AddUserAccountRepository $addUserAccountRepository;
    private AddUserInvitationRepository $addUserInvitationRepository;
    private DeleteUserRepository $deleteUserRepository;
    private GetUserIdService $getUserIdService;
    private AddUserAccountInfoService $addUserAccountInfoService;
    private ValidateAddUserDataService $validateAddUserDataService;

    public function __construct(PDO $connection)
    {
        $this->addUserAccountRepository = new AddUserAccountRepository($connection);
        $this->addUserInvitationRepository = new AddUserInvitationRepository($connection);
        $this->deleteUserRepository = new DeleteUserRepository($connection);
        $this->addUserAccountInfoService = new AddUserAccountInfoService($connection);
        $this->validateAddUserDataService = new ValidateAddUserDataService();
        $this->getUserIdService = new GetUserIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addUser(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);

        $serviceData = array_merge($data, [
            'username' => $this->getUserIdService->getUniqueId(64),
            'userStatus' => UserStatus::active->name
        ]);
        $sanitizedData = SanitizeUserDataService::sanitizeData($serviceData);
        $validatedData = $this->validateAddUserDataService->validateData($sanitizedData);

        try {
            /* Add user account info */
            $this->addUserAccountInfoService->addUserInfo($serviceData);
            /* Add user account */
            $userData = MapUserDataService::mapData($validatedData);
            /* Re-inject accountNo & hash lost during sanitation */
            $userData->account_no = $data['accountNo'];
            $userData->user_hash = $validatedData['userHash'];
            if ($this->addUserAccountRepository->addAccount($userData)) {
                /* Add user invitation */
                if ($this->addUserInvitationRepository->inviteUser($userData->username)) {
                    // TODO: Send user invitation
                    return [
                        'success' => 'New user added',
                        'id' => $userData->username
                    ];
                }
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
