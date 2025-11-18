<?php

namespace App\Domain\SaleTask\Service\AddSaleTask;

use App\Exception\ValidationException;

final class ValidateAddSaleTaskDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        if (empty($data['saleId'])) {
            throw new ValidationException('Invalid or missing sale details');
        }

        if (empty($data['taskId'])) {
            throw new ValidationException('Invalid or missing sale task details');
        }

        return $data;
    }
}
