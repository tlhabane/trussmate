<?php

namespace App\Domain\SaleTask\Service\UpdateSaleTask;

use App\Exception\ValidationException;

final class ValidateUpdateSaleTaskDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        if (empty($data['saleTaskId'])) {
            throw new ValidationException('Invalid or missing sale task details');
        }

        if (empty($data['comments'])) {
            throw new ValidationException('Data validation error', 422, [
                'comments' => 'Invalid reason or comment provided'
            ]);
        }

        return $data;
    }
}
