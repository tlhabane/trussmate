<?php

namespace App\Domain\CustomerAddress\Data;

final class CustomerAddressData
{
    /* FK: Address owner */
    public string $customer_id;
    /* Address info */
    public string $address_id;
    public int $billing_address;
    public string $unit_no;
    public string $complex_name;
    public string $country;
    public string $province;
    public string $city;
    public string $suburb;
    public string $street_address;
    public string $full_address;
    public string $postal_code;
    public string $place_id;
    public float $latitude;
    public float $longitude;
    /* Search and filter */
    public string $search;
}
