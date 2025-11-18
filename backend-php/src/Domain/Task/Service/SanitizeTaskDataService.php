<?php

namespace App\Domain\Task\Service;

use App\Util\Utilities;

final class SanitizeTaskDataService
{
    public static function sanitizeData(array $data): array
    {
        return [
            'taskId' => Utilities::sanitizeString($data['taskId'] ?? ''),
            'taskName' => Utilities::sanitizeAndEncodeString($data['taskName'] ?? ''),
            'taskDescription' => Utilities::sanitizeAndEncodeString($data['taskDescription'] ?? ''),
            'taskPayment' => floatval(Utilities::sanitizeString($data['taskPayment'] ?? '')),
            'taskDays' => intval(Utilities::sanitizeString($data['taskDays'] ?? '')),
            'taskPaymentType' => Utilities::sanitizeString($data['taskPaymentType'] ?? ''),
            'taskFrequency' => intval(Utilities::sanitizeString($data['taskFrequency'] ?? '')),
            'taskDocument' => intval(Utilities::sanitizeString($data['taskDocument'] ?? '')),
            'taskAction' => Utilities::sanitizeString($data['taskAction'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
