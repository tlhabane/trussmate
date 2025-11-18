<?php

namespace App\Domain\UserSession\Service;

use App\Domain\UserSession\Data\UserSessionData;
use App\Domain\User\Service\GetUserRoleService;

final class MapSessionDataService
{
    public static function mapData(array $sanitizedData): UserSessionData
    {
        $data = new UserSessionData();
        $data->account_no = $sanitizedData['accountNo'];
        $data->user_id = $sanitizedData['username'];
        $data->user_role = GetUserRoleService::getUserRole($sanitizedData['userRole']);
        $data->session_status = GetSessionStatusService::getStatus($sanitizedData['sessionStatus']);
        $data->session_hash = $sanitizedData['sessionHash'] ?? '';
        $data->session_expiry = $sanitizedData['sessionExpiry'] ?? '';
        $data->device_id = $sanitizedData['deviceId'];

        return $data;
    }
}
