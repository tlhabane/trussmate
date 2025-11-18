<?php

namespace App\Domain\BankAccount\Service;

use App\Exception\ValidationException;

final class ValidateAddUpdateBankAccountData
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        $fields = [];

        if (empty($data['bankName'])) {
            $fields['bankName'] = 'Invalid bank name provided.';
        }
        if (empty($data['bankAccountNo'])) {
            $fields['bankAccountNo'] = 'Invalid account number provided.';
        }
        if (empty($data['bankAccountName'])) {
            $fields['bankAccountName'] = 'Invalid account name provided.';
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error.', 422, $fields);
        }

        return $data;
    }
}
