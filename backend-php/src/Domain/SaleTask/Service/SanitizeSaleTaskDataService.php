<?php

namespace App\Domain\SaleTask\Service;

use App\Exception\ValidationException;
use App\Util\Utilities;

final class SanitizeSaleTaskDataService
{
    /**
     * @throws ValidationException
     */
    public static function sanitizeData(array $data): array
    {
        return [
            'saleTaskId' => Utilities::sanitizeString($data['saleTaskId'] ?? ''),
            'saleId' => Utilities::sanitizeString($data['saleId'] ?? ''),
            'taskId' => Utilities::sanitizeString($data['taskId'] ?? ''),
            'taskNo' => intval(Utilities::sanitizeString($data['taskNo'] ?? '')),
            'taskStatus' => Utilities::sanitizeString($data['taskStatus'] ?? ''),
            'taskAction' => Utilities::sanitizeString($data['taskAction'] ?? ''),
            'taskPayment' => floatval(Utilities::sanitizeString($data['taskPayment'] ?? '')),
            'taskCompletionDate' => Utilities::sanitizeDateAndTime($data['taskCompletionDate'] ?? ""),
            'taskDays' => intval(Utilities::sanitizeString($data['taskDays'] ?? '')),
            'taskPaymentType' => Utilities::sanitizeString($data['taskPaymentType'] ?? ''),
            'taskFrequency' => intval(Utilities::sanitizeString($data['taskFrequency'] ?? '')),
            'customerId' => Utilities::sanitizeString($data['customerId'] ?? ''),
            'comments' => Utilities::sanitizeAndEncodeString($data['comments'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
