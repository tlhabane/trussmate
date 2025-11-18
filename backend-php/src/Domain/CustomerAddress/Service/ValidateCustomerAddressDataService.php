<?php

namespace App\Domain\CustomerAddress\Service;

use App\Exception\ValidationException;

final class ValidateCustomerAddressDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        if (empty($data['customerId'])) {
            throw new ValidationException('Invalid or missing customer details');
        }
        if (empty($data['fullAddress'])) {
            throw new ValidationException('Data validation error', 422, [
                'fullAddress' => 'Invalid address provided'
            ]);
        }

        return $data;
    }
}
