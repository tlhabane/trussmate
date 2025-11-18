<?php

namespace App\Domain\CustomerAddress\Repository;

use App\Domain\CustomerAddress\Data\MapInsertUpdateAddressData;
use App\Domain\CustomerAddress\Data\CustomerAddressData;
use PDO;

final class AddCustomerAddressRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addAddress(CustomerAddressData $data): bool
    {
        $query = "INSERT INTO customer_address SET 
                  customer_id = :customer_id,
                  address_id = :address_id,
                  billing_address = :billing_address,
                  place_id = :place_id,
                  unit_no = :unit_no,
                  complex_name = :complex_name,
                  full_address = :full_address,
                  street_address = :street_address,
                  province = :province,
                  city = :city,
                  suburb = :suburb,
                  country = :country,
                  postal_code = :postal_code,
                  latitude = :latitude,
                  longitude = :longitude";

        $query_stmt = $this->connection->prepare($query);
        $query_data = array_merge(MapInsertUpdateAddressData::map($data), [
            'customer_id' => $data->customer_id
        ]);
        return $query_stmt->execute($query_data);
    }
}
