<?php

namespace App\Domain\Sale\Service;

use App\Exception\ValidationException;
use App\Util\Utilities;

final class SanitizeSaleDataService
{
    /**
     * @throws ValidationException
     */
    public static function sanitizeData(array $data): array
    {
        return [
            'saleId' => Utilities::sanitizeString($data['saleId'] ?? ''),
            'saleNo' => intval(Utilities::sanitizeString($data['saleNo'] ?? '')),
            'saleStatus' => Utilities::sanitizeString($data['saleStatus'] ?? ''),
            'customerId' => Utilities::sanitizeString($data['customerId'] ?? ''),
            'contactId' => Utilities::sanitizeString($data['contactId'] ?? ''),
            'billingAddressId' => Utilities::sanitizeString($data['billingAddressId'] ?? ''),
            'deliveryAddressId' => Utilities::sanitizeString($data['deliveryAddressId'] ?? ''),
            'workflowId' => Utilities::sanitizeString($data['workflowId'] ?? ''),
            'deliveryRequired' => intval(Utilities::sanitizeString($data['delivery'] ?? '')),
            'labourRequired' => intval(Utilities::sanitizeString($data['labour'] ?? '')),
            'startDate' => Utilities::sanitizeDateAndTime($data['startDate'] ?? ''),
            'endDate' => Utilities::sanitizeDateAndTime($data['endDate'] ?? ''),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0)),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
        ];
    }
}
