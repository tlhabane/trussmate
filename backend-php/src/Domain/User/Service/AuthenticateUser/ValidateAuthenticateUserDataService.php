<?php

namespace App\Domain\User\Service\AuthenticateUser;

use App\Contract\DataValidationContract;
use App\Domain\User\Service\GetUserByIdService;
use App\Domain\User\Service\GetUserStatusService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use App\Util\Utilities;
use PDO;

final class ValidateAuthenticateUserDataService implements DataValidationContract
{
    private GetUserByIdService $getUserByIdService;

    public function __construct(PDO $connection)
    {
        $this->getUserByIdService = new GetUserByIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function validateData(array $data): array
    {
        $fields = [];

        $users = $this->getUserByIdService->getUser($data['username']);
        if (empty($data['username']) || count($users['records']) !== 1) {
            $fields['username'] = 'Invalid email/phone number provided*';
        }

        if (empty($data['password'])) {
            $fields['password'] = 'Invalid password provided';
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        $userData = [];
        foreach ($users['records'] as $user) {
            $passwordValid = Utilities::validatePassword($data['password'], $user['userHash']);
            if (!$passwordValid) {
                throw new RuntimeException('Invalid email/phone number/password provided.', 401);
            }
            $user_status = GetUserStatusService::getStatus($user['userStatus']);
            if ($user_status->value > 1) {
                throw new RuntimeException('Please contact your line manager.', 403);
            }

            $userData = [
                'accountNo' => $user['accountNo'],
                'username' => $user['username'],
                'regionId' => $user['regionId'],
                'regionName' => $user['regionName'],
                'firstName' => $user['firstName'],
                'userRole' => $user['userRole'],
            ];
        }

        return $userData;
    }
}
