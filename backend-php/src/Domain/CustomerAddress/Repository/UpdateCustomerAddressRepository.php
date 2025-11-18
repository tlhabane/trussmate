<?php

namespace App\Domain\CustomerAddress\Repository;

use App\Domain\CustomerAddress\Data\MapInsertUpdateAddressData;
use App\Domain\CustomerAddress\Data\CustomerAddressData;
use PDO;

final class UpdateCustomerAddressRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateAddress(CustomerAddressData $data): bool
    {
        $query = "UPDATE customer_address SET 
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
                      longitude = :longitude 
                  WHERE 
                      address_id = :address_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapInsertUpdateAddressData::map($data);
        return $query_stmt->execute($query_data);
    }
}
