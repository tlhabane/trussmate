<?php

namespace App\Domain\Invoice\Service;

use App\Exception\ValidationException;

final class ValidateAddInvoiceDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        if (empty($data['saleTaskId'])) {
            throw new ValidationException('Invalid or missing task details');
        }

        if (empty($data['invoiceType'])) {
            throw new ValidationException('Invalid invoice type');
        }

        return $data;
    }
}
