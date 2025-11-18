<?php

namespace App\Domain\SaleJob\Service;

use App\Util\Utilities;

final class SanitizeSaleJobDataService
{
    public static function sanitizeData(array $data): array
    {
        return [
            'saleId' => Utilities::sanitizeString($data['saleId'] ?? ''),
            'jobNo' => Utilities::sanitizeString($data['jobNo'] ?? ''),
            'jobDescription' => Utilities::sanitizeAndEncodeString($data['jobDescription'] ?? ''),
            'designInfo' => Utilities::sanitizeString((string)json_encode($data['designInfo'] ?? '')),
            'lineItems' => Utilities::sanitizeString((string)json_encode($data['lineItems'] ?? '')),
            'subtotal' => floatval(Utilities::sanitizeString($data['subtotal'] ?? '')),
            'vat' => floatval(Utilities::sanitizeString($data['vat'] ?? '')),
            'total' => floatval(Utilities::sanitizeString($data['total'] ?? ''))
        ];
    }
}
