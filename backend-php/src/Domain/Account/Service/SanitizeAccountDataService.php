<?php

namespace App\Domain\Account\Service;

use App\Contract\SanitizeDataContract;
use App\Util\Utilities;

final class SanitizeAccountDataService implements SanitizeDataContract
{
    public static function sanitizeData(array $data): array
    {
        return [
            'accountStatus' => Utilities::sanitizeString($data['accountStatus'] ?? ''),
            'registrationNo' => Utilities::sanitizeString($data['registrationNo'] ?? ''),
            'registeredName' => Utilities::sanitizeAndEncodeString($data['registeredName'] ?? ''),
            'vatNo' => Utilities::sanitizeString($data['vatNo'] ?? ''),
            'tradingName' => Utilities::sanitizeAndEncodeString($data['tradingName'] ?? ''),
            'tel' => Utilities::sanitizeString($data['tel'] ?? ''),
            'altTel' => Utilities::sanitizeString($data['altTel'] ?? ''),
            'email' => Utilities::sanitizeEmail($data['email'] ?? ''),
            'web' => Utilities::sanitizeString($data['web'] ?? ''),
            'address' => Utilities::sanitizeAndEncodeString($data['address'] ?? '')
        ];
    }
}
