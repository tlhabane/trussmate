<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerType;

final class GetCustomerTypeService
{
    public static function getType(string $customer_type): CustomerType
    {
        return match (trim(strtolower($customer_type))) {
            'business' => CustomerType::business,
            'individual' => CustomerType::individual,
            default => CustomerType::all
        };
    }
}
