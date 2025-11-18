<?php

namespace App\Domain\UserSession\Service;

use App\Domain\UserSession\Data\SessionStatus;

final class GetSessionStatusService
{
    public static function getStatus(string $session_status): SessionStatus
    {
        return match (strtolower($session_status)) {
            'ended' => SessionStatus::ended,
            'expired' => SessionStatus::expired,
            default => SessionStatus::active
        };
    }
}
