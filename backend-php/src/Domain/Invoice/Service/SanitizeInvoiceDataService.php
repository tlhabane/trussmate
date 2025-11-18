<?php

namespace App\Domain\Invoice\Service;

use App\Exception\ValidationException;
use App\Util\Utilities;

final class SanitizeInvoiceDataService
{
    /**
     * @throws ValidationException
     */
    public static function sanitizedData(array $data): array
    {
        return [
            'invoiceNo' => Utilities::sanitizeString($data['invoiceNo'] ?? ''),
            'saleTaskId' => Utilities::sanitizeString($data['saleTaskId'] ?? ''),
            'invoiceType' => Utilities::sanitizeString($data['invoiceType'] ?? ''),
            'saleId' => Utilities::sanitizeString($data['saleId'] ?? ''),
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
