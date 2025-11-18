<?php

namespace App\Domain\UserSession\Service;

use App\Util\Utilities;

final class SanitizeSessionDataService
{
    public static function sanitizeData(array $data): array
    {
        return [
            'accountNo' => Utilities::sanitizeString($data['accountNo'] ?? ''),
            'username' => Utilities::sanitizeString($data['username'] ?? ''),
            'userRole' => Utilities::sanitizeString($data['userRole'] ?? ''),
            'sessionIp' => Utilities::sanitizeString($data['sessionIp'] ?? '127.0.0.1'),
            'sessionId' => Utilities::sanitizeString($data['sessionId'] ?? ''),
            'sessionKey' => Utilities::sanitizeString($data['sessionKey'] ?? ''),
            'sessionToken' => Utilities::sanitizeString($data['token'] ?? ''),
            'deviceId' => Utilities::sanitizeString($data['deviceId'] ?? '')
        ];
    }
}
