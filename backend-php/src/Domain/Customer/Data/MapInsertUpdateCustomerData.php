<?php

namespace App\Domain\Customer\Data;

class MapInsertUpdateCustomerData
{
    public static function map(CustomerData $data): array
    {
        return [
            'customer_id' => $data->customer_id,
            'customer_type' => $data->customer_type->value,
            'customer_name' => $data->customer_name,
            'registration_no' => $data->registration_no,
            'vat_no' => $data->vat_no,
            'tel' => $data->tel,
            'alt_tel' => $data->alt_tel,
            'email' => $data->email,
            'web' => $data->web
        ];
    }
}
