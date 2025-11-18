<?php

namespace App\Domain\User\Service\AddUser;

use App\Domain\User\Service\GetUserRoleService;
use App\Contract\DataValidationContract;
use App\Exception\ValidationException;
use App\Util\Utilities;

final class ValidateAddUserDataService implements DataValidationContract
{
    public function validateData(array $data): array
    {
        $userRole = GetUserRoleService::getUserRole($data['userRole'] ?? '');
        if ($userRole->value === 0) {
            throw new ValidationException('Data validation error', 422, [
                'userRole' => 'Invalid user role provided'
            ]);
        }

        if (empty($data['password']) || trim($data['password']) === '') {
            return array_merge($data, [
                'userHash' => Utilities::passwordHash(Utilities::generateToken(8))
            ]);
        }

        return array_merge($data, [
            'userHash' => Utilities::passwordHash(trim($data['password']))
        ]);
    }
}
