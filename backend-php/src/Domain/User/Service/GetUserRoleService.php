<?php

namespace App\Domain\User\Service;

use App\Domain\User\Data\UserRole;

final class GetUserRoleService
{
    public static function getUserRole(string $user_role): UserRole
    {
        return match (strtolower($user_role)) {
            'super_admin' => UserRole::super_admin,
            'admin' => UserRole::admin,
            'estimator' => UserRole::estimator,
            'manufacturer', 'production' => UserRole::production,
            'customer' => UserRole::customer,
            'user' => UserRole::user,
            'system' => UserRole::system,
            default => UserRole::none
        };
    }
}
