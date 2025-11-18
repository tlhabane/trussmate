<?php

namespace App\Domain\ContactPerson\Service;

use App\Util\Utilities;

final class SanitizeContactPersonDataService
{
    public static function sanitizeData(array $data): array
    {
        return  [
            'customerId' => Utilities::sanitizeString($data['customerId'] ?? ''),
            'contactId' => Utilities::sanitizeString($data['contactId'] ?? ''),
            'firstName' => Utilities::sanitizeAndEncodeString($data['firstName'] ?? ''),
            'lastName' => Utilities::sanitizeAndEncodeString($data['lastName'] ?? ''),
            'jobTitle' => Utilities::sanitizeAndEncodeString($data['jobTitle'] ?? ''),
            'tel' => Utilities::sanitizeString($data['tel'] ?? ''),
            'altTel' => Utilities::sanitizeString($data['altTel'] ?? ''),
            'email' => Utilities::sanitizeEmail($data['email'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
