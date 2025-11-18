<?php

namespace App\Domain\User\Service;

use App\Domain\User\Data\UserData;
use App\Domain\User\Data\InvitationStatus;

final class MapUserDataService
{
    public static function mapData(array $sanitizedData): UserData
    {
        $data = new UserData();
        $data->username = $sanitizedData['username'];
        $data->user_status = GetUserStatusService::getStatus($sanitizedData['userStatus']);
        $data->user_role = GetUserRoleService::getUserRole($sanitizedData['userRole']);
        $data->region_id = $sanitizedData['regionId'];
        $data->first_name = $sanitizedData['firstName'];
        $data->last_name = $sanitizedData['lastName'];
        $data->job_title = $sanitizedData['jobTitle'];
        $data->tel = $sanitizedData['tel'];
        $data->alt_tel = $sanitizedData['altTel'];
        $data->email = $sanitizedData['email'];
        $data->invitation_status = match (strtolower('invitationStatus')) {
            'accepted' => InvitationStatus::accepted,
            'rejected' => InvitationStatus::rejected,
            'expired' => InvitationStatus::expired,
            default => InvitationStatus::pending
        };
        $data->search = $sanitizedData['search'];

        return $data;
    }
}
