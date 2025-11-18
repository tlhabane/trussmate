<?php

namespace App\Domain\SaleDocument\Service;

use App\Util\Utilities;

final class SanitizeSaleDocumentDataService
{
    public static function sanitizeData(array $data): array
    {
        return [
            'saleId' => Utilities::sanitizeString($data['saleId'] ?? ''),
            'saleTaskId' => Utilities::sanitizeString($data['saleTaskId'] ?? ''),
            'docId' => Utilities::sanitizeString($data['docId'] ?? ''),
            'docType' => Utilities::sanitizeString($data['docType'] ?? ''),
            'docSrc' => Utilities::sanitizeString($data['docSrc'] ?? ''),
            'docName' => Utilities::sanitizeAndEncodeString($data['docName'] ?? '')
        ];
    }
}
