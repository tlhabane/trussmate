<?php

namespace App\Domain\SaleJob\Service;

use App\Exception\ValidationException;

final class ValidateSaleJobDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        if (empty($data['saleId'])) {
            throw new ValidationException('Invalid or missing sale details');
        }
        if (empty($data['jobNo'])) {
            throw new ValidationException('Invalid or missing job no', 422, $data);
        }
        if (empty($data['lineItems'])) {
            throw new ValidationException('Invalid or missing sale items');
        }

        return $data;
    }
}
