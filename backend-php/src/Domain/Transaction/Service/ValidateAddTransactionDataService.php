<?php

namespace App\Domain\Transaction\Service;

use App\Exception\ValidationException;

final class ValidateAddTransactionDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        $fields = [];
        if (empty($data['invoiceNo']) && empty($data['saleTaskId'])) {
            $fields['invoiceNo'] = 'Invalid invoice number provided';
        }

        if (empty($data['transactionAmount'])) {
            $fields['transactionAmount'] = 'Invalid transaction amount provided';
        }

        if (empty($data['transactionDesc'])) {
            $fields['transactionDesc'] = 'Invalid transaction description or reference provided';
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
