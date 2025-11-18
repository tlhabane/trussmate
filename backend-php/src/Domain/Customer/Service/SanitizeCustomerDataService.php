<?php

namespace App\Domain\Customer\Service;

use App\Util\Utilities;

final class SanitizeCustomerDataService
{
    public static function sanitizeData(array $data): array
    {
        return [
            'customerId' => Utilities::sanitizeString($data['customerId'] ?? ''),
            'customerType' => Utilities::sanitizeString($data['customerType'] ?? ''),
            'customerName' => Utilities::sanitizeAndEncodeString($data['customerName'] ?? ''),
            'registrationNo' => Utilities::sanitizeString($data['registrationNo'] ?? ''),
            'vatNo' => Utilities::sanitizeString($data['vatNo'] ?? ''),
            'tel' => Utilities::sanitizeString($data['tel'] ?? ''),
            'altTel' => Utilities::sanitizeString($data['altTel'] ?? ''),
            'email' => Utilities::sanitizeEmail($data['email'] ?? ''),
            'web' => Utilities::sanitizeUrl($data['web'] ?? ''),
            'address' => Utilities::sanitizeAndEncodeString($data['address'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
