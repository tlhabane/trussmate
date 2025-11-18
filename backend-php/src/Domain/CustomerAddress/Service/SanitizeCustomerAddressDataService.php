<?php

namespace App\Domain\CustomerAddress\Service;

use App\Util\Utilities;

final class SanitizeCustomerAddressDataService
{
    public static function sanitizeData(array $data): array
    {
        return [
            'customerId' => Utilities::sanitizeString($data['customerId'] ?? ''),
            'addressId' => Utilities::sanitizeString($data['addressId'] ?? ''),
            'billingAddress' => intval(Utilities::sanitizeString($data['billingAddress'] ?? '')),
            'unitNo' => Utilities::sanitizeAndEncodeString($data['unitNo'] ?? ''),
            'complexName' => Utilities::sanitizeAndEncodeString($data['complexName'] ?? ''),
            'fullAddress' => Utilities::sanitizeAndEncodeString($data['fullAddress'] ?? ''),
            'country' => Utilities::sanitizeAndEncodeString($data['country'] ?? ''),
            'province' => Utilities::sanitizeAndEncodeString($data['province'] ?? ''),
            'city' => Utilities::sanitizeAndEncodeString($data['city'] ?? ''),
            'suburb' => Utilities::sanitizeAndEncodeString($data['suburb'] ?? ''),
            'streetAddress' => Utilities::sanitizeAndEncodeString($data['streetAddress'] ?? ''),
            'postalCode' => Utilities::sanitizeAndEncodeString($data['postalCode'] ?? ''),
            'placeId' => Utilities::sanitizeString($data['placeId'] ?? ''),
            'latitude' => floatval(Utilities::sanitizeString($data['latitude'] ?? '')),
            'longitude' => floatval(Utilities::sanitizeString($data['longitude'] ?? '')),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
