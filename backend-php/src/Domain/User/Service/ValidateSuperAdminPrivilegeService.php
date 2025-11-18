<?php

namespace App\Domain\User\Service;

use App\Exception\RuntimeException;

final class ValidateSuperAdminPrivilegeService
{
    /**
     * @throws RuntimeException
     */
    public static function validate(string $user_role): void
    {
        /* Validate user privileges */
        $userRole = GetUserRoleService::getUserRole($user_role);
        if ($userRole->value !== 1) {
            throw new RuntimeException('You\'re not authorised to perform this function.', 403);
        }
    }
}
