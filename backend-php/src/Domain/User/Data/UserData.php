<?php

namespace App\Domain\User\Data;

final class UserData
{
    public string $account_no;
    /* Login credential */
    public string $username;
    public UserStatus $user_status;
    public UserRole $user_role;
    public string $user_hash;
    /* User info */
    public string $region_id;
    public string $first_name;
    public string $last_name;
    public string $tel;
    public string $alt_tel;
    public string $email;
    public string $job_title;
    /* User Invitation */
    public InvitationStatus $invitation_status;
    /* Search and filter */
    public string $search;
}
