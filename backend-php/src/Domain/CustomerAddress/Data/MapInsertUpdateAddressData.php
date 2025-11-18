<?php

namespace App\Domain\CustomerAddress\Data;

class MapInsertUpdateAddressData
{
    public static function map(CustomerAddressData $data): array
    {
        return [
            'address_id' => $data->address_id,
            'billing_address' => $data->billing_address,
            'place_id' => $data->place_id,
            'unit_no' => $data->unit_no,
            'complex_name' => $data->complex_name,
            'full_address' => $data->full_address,
            'street_address' => $data->street_address,
            'province' => $data->province,
            'city' => $data->city,
            'suburb' => $data->suburb,
            'country' => $data->country,
            'postal_code' => $data->postal_code,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude
        ];
    }
}
