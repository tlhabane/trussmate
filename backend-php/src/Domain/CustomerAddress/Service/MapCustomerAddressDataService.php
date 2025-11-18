<?php

namespace App\Domain\CustomerAddress\Service;

use App\Domain\CustomerAddress\Data\CustomerAddressData;

final class MapCustomerAddressDataService
{
    public static function map(array $data): CustomerAddressData
    {
        $address = new CustomerAddressData();
        $address->customer_id = $data['customerId'];
        $address->address_id = $data['addressId'];
        $address->billing_address = $data['billingAddress'];
        $address->unit_no = $data['unitNo'];
        $address->complex_name = $data['complexName'];
        $address->full_address = $data['fullAddress'];
        $address->country = $data['country'];
        $address->province = $data['province'];
        $address->city = $data['city'];
        $address->suburb = $data['suburb'];
        $address->street_address = $data['streetAddress'];
        $address->postal_code = $data['postalCode'];
        $address->place_id = $data['placeId'];
        $address->latitude = $data['latitude'];
        $address->longitude = $data['longitude'];

        return $address;
    }
}
