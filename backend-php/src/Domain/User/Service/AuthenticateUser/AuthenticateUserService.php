<?php

namespace App\Domain\User\Service\AuthenticateUser;

use App\Domain\User\Service\SanitizeUserDataService;
use App\Domain\UserSession\Service\AddSession\AddSessionService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AuthenticateUserService
{
    private AddSessionService $addSessionService;
    private ValidateAuthenticateUserDataService $validateAuthenticateUserDataService;

    public function __construct(PDO $connection)
    {
        $this->addSessionService = new AddSessionService($connection);
        $this->validateAuthenticateUserDataService = new ValidateAuthenticateUserDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function authenticateUser(array $data): array
    {
        $sanitizedData = SanitizeUserDataService::sanitizeData($data);
        $validatedData = $this->validateAuthenticateUserDataService->validateData($sanitizedData);
        $sessionData = $this->addSessionService->addSession($validatedData);

        return [
            'token' => $sessionData['token'],
            'accountNo' => $validatedData['accountNo'],
            'username' => $validatedData['username'],
            'regionId' => $validatedData['regionId'],
            'regionName' => $validatedData['regionName'],
            'firstName' => $validatedData['firstName'],
            'userRole' => $validatedData['userRole'],
        ];
    }
}
