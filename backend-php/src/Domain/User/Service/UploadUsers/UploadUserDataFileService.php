<?php

namespace App\Domain\User\Service\UploadUsers;

use App\Domain\User\Repository\DeleteUserRepository;
use App\Domain\User\Service\AddUser\AddUserService;
use App\Domain\User\Service\AuthenticateUser\AuthenticateUserService;
use App\Domain\UserSession\Service\ValidateSession\ValidateSessionService;
use App\Domain\UserSession\Service\TerminateSession\TerminateSessionService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UploadUserDataFileService
{
    private DeleteUserRepository $deleteUserRepository;
    private ValidateSessionService $validateSessionService;
    private TerminateSessionService $terminateSessionService;
    private AddUserService $addUserService;
    private AuthenticateUserService $authenticateUserService;

    private ValidateUserDataFileService $validateUserDataFileService;

    public function __construct(PDO $connection) {
        $this->authenticateUserService = new AuthenticateUserService($connection);
        $this->validateSessionService = new ValidateSessionService($connection);
        $this->terminateSessionService = new TerminateSessionService($connection);
        $this->addUserService = new AddUserService($connection);
        $this->deleteUserRepository = new DeleteUserRepository($connection);
        $this->validateUserDataFileService = new ValidateUserDataFileService();
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function uploadUsers(array $data): array
    {
        $users_added = [];
        $session_id = '';
        try {
            $login = $this->authenticateUserService->authenticateUser([
                'username' => $data['username'],
                'password' => $data['password']
            ]);
            $tokenBits = str_split($login['token'], 32);
            $sessionData = $this->validateSessionService->validateSession([
                'sessionId' => $tokenBits[1],
                'sessionKey' => $tokenBits[0]
            ]);
            $session_id = $tokenBits[1];

            $validatedFiles = $this->validateUserDataFileService->validateData($data['files']);
            foreach ($validatedFiles as $validatedFile) {
                $users = LoadUsersFromFileService::loadCustomers($validatedFile);
                foreach ($users as $user) {
                    $add_user = $this->addUserService->addUser(array_merge($sessionData, $user));
                    $users_added[] = $add_user['id'];
                }
            }
            $this->terminateSessionService->terminateSession([
                'sessionId' => $session_id
            ]);
            return [
                'success' => sprintf('%s user records created', count($users_added)),
            ];
        }catch (RuntimeException | ValidationException $exception) {
            // rollback
            foreach ($users_added as $id) {
                $this->deleteUserRepository->deleteUser($id);
            }
            // logout
            if ($session_id !== '') {
                $this->terminateSessionService->terminateSession([
                    'sessionId' => $session_id
                ]);
            }
            // rethrow exception(s)
            if ($exception instanceof ValidationException) {
                throw new ValidationException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getErrors()
                );
            }

            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }
    }
}
