<?php

namespace App\Domain\Transaction\Service;

use App\Exception\ValidationException;
use App\Util\Utilities;

final class SanitizeTransactionDataService
{
    /**
     * @throws ValidationException
     */
    public static function sanitizeData(array $data): array
    {
        return [
            'invoiceNo' => intval(Utilities::sanitizeString($data['invoiceNo'] ?? '')),
            'transactionId' => Utilities::sanitizeString($data['transactionId'] ?? ''),
            'transactionType' => Utilities::sanitizeString($data['transactionType'] ?? ''),
            'transactionAmount' => floatval(Utilities::sanitizeString($data['transactionAmount'] ?? '')),
            'transactionDate' => Utilities::sanitizeDateAndTime($data['transactionDate'] ?? ''),
            'transactionMethod' => Utilities::sanitizeString($data['transactionMethod'] ?? ''),
            'transactionDesc' => Utilities::sanitizeAndEncodeString($data['transactionDesc'] ?? ''),
            'saleId' => Utilities::sanitizeString($data['saleId'] ?? ''),
            'saleTaskId' => Utilities::sanitizeString($data['saleTaskId'] ?? ''),
            'customerId' => Utilities::sanitizeString($data['customerId'] ?? ''),
            'contactId' => Utilities::sanitizeString($data['contactId'] ?? ''),
            'startDate' => Utilities::sanitizeDateAndTime($data['startDate'] ?? ''),
            'endDate' => Utilities::sanitizeDateAndTime($data['endDate'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
