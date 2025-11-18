<?php

namespace App\Domain\User\Service;

use App\Domain\User\Data\UserStatus;

final class GetUserStatusService
{
    public static function getStatus(string $user_status): UserStatus
    {
        return match (strtolower($user_status)) {
            'active' => UserStatus::active,
            'locked' => UserStatus::locked,
            'inactive' => UserStatus::inactive,
            default => UserStatus::none
        };
    }
}
