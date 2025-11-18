<?php

namespace App\Domain\User\Service;

use App\Contract\SanitizeDataContract;
use App\Util\Utilities;

final class SanitizeUserDataService implements SanitizeDataContract
{
    public static function sanitizeData(array $data): array
    {
        return [
            'username' => Utilities::sanitizeString($data['username'] ?? ''),
            'password' => Utilities::sanitizeString($data['password'] ?? ''),
            'password1' => Utilities::sanitizeString($data['password1'] ?? ''),
            'password2' => Utilities::sanitizeString($data['password2'] ?? ''),
            'userStatus' => Utilities::sanitizeString($data['userStatus'] ?? ''),
            'userRole' => Utilities::sanitizeString($data['userRole'] ?? ''),
            'regionId' => Utilities::sanitizeString($data['regionId'] ?? ''),
            'firstName' => Utilities::sanitizeAndEncodeString($data['firstName'] ?? ''),
            'lastName' => Utilities::sanitizeAndEncodeString($data['lastName'] ?? ''),
            'tel' => Utilities::sanitizeString($data['tel'] ?? ''),
            'altTel' => Utilities::sanitizeString($data['altTel'] ?? ''),
            'email' => Utilities::sanitizeEmail($data['email'] ?? ''),
            'jobTitle' => Utilities::sanitizeAndEncodeString($data['jobTitle'] ?? ''),
            'invitationStatus' => Utilities::sanitizeString($data['invitationStatus'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
