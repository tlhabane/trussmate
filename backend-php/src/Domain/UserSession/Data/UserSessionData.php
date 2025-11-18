<?php

namespace App\Domain\UserSession\Data;

use App\Domain\User\Data\UserRole;

final class UserSessionData
{
    public string $account_no;
    public string $user_id;
    public UserRole $user_role;
    public string $session_id;
    public SessionStatus $session_status;
    public string $session_hash;
    public string $session_expiry;
    public string $device_id;
}
