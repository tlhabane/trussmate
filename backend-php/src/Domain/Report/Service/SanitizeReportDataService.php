<?php

namespace App\Domain\Report\Service;

use App\Exception\ValidationException;
use App\Util\Utilities;

final class SanitizeReportDataService
{
    /**
     * @throws ValidationException
     */
    public static function sanitize(array $data): array
    {
        return [
            'customerId' => Utilities::sanitizeString($data['customerId'] ?? ''),
            'startDate' => Utilities::sanitizeDateAndTime($data['startDate'] ?? ''),
            'endDate' => Utilities::sanitizeDateAndTime($data['endDate'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
