<?php

namespace App\Domain\CustomerAddress\Repository;

use App\Domain\CustomerAddress\Data\CustomerAddressData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetCustomerAddressRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAddress(CustomerAddressData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      customer_id, address_id, billing_address, unit_no, complex_name, full_address, place_id, 
                      street_address, suburb, city, municipality, province, country, postal_code, latitude, longitude
                  FROM 
                      customer_address 
                  WHERE 
                      customer_id = :customer_id";

        $query .= empty($data->address_id) ? "" : " AND address_id = :address_id";
        $query .= empty($data->billing_address) ? "" : " AND billing_address = :billing_address";
        if (!empty($data->search)) {
            $query .= " AND (
                LOWER(full_address) LIKE :search OR LOWER(street_address) LIKE :search OR 
                LOWER(suburb) LIKE :search OR LOWER(city) LIKE :search OR LOWER(municipality) LIKE :search OR 
                LOWER(province) LIKE :search OR LOWER(country) LIKE :search
            )";
        }
        $query .= " ORDER BY full_address ASC";
        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':customer_id', $data->customer_id);
        if (!empty($data->address_id)) {
            $query_stmt->bindParam(':address_id', $data->address_id);
        }
        if (!empty($data->billing_address)) {
            $query_stmt->bindParam(':billing_address', $data->billing_address, PDO::PARAM_INT);
        }
        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
