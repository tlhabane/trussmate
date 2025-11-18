<?php

namespace App\Domain\Sale\Service\AddSale;

use App\Exception\ValidationException;

final class ValidateAddSaleDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        $fields = [];

        if (empty($data['customerId'])) {
            $fields['customerId'] = 'Invalid customer provided';
        }

        /*if (empty($data['contactId'])) {
            $fields['contactId'] = 'Invalid contact provided';
        }*/

        if (empty($data['workflowId'])) {
            $fields['workflowId'] = 'Invalid sales process provided';
        }

        if ($data['deliveryRequired'] === 1 && empty($data['billingAddressId']) && empty($data['deliveryAddressId'])) {
            $fields['deliveryAddressId'] = 'Invalid delivery address provided';
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
