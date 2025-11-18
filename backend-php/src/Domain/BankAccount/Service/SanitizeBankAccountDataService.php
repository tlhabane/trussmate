<?php

namespace App\Domain\BankAccount\Service;

use App\Util\Utilities;

final class SanitizeBankAccountDataService
{
    public static function sanitizeData(array $data): array
    {
        return [
            'bankId' => Utilities::sanitizeString($data['bankId'] ?? ''),
            'bankName' => Utilities::sanitizeAndEncodeString($data['bankName'] ?? ''),
            'bankAccountName' => Utilities::sanitizeAndEncodeString($data['bankAccountName'] ?? ''),
            'bankAccountNo' => Utilities::sanitizeString($data['bankAccountNo'] ?? ''),
            'branchCode' => Utilities::sanitizeString($data['branchCode'] ?? ''),
            'recordsPerPage' => intval(Utilities::sanitizeString($data['recordsPerPage'] ?? 0)),
            'search' => Utilities::sanitizeAndEncodeString($data['search'] ?? ''),
            'page' => intval(Utilities::sanitizeString($data['page'] ?? 0))
        ];
    }
}
