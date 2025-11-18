<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Data\MapInsertUpdateCustomerData;
use PDO;

final class UpdateCustomerRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateCustomer(CustomerData $data): bool
    {
        $query = "UPDATE customer SET 
                      customer_type = :customer_type,
                      customer_name = :customer_name,
                      registration_no = :registration_no,
                      vat_no = :vat_no,
                      tel = :tel,
                      alt_tel = :alt_tel,
                      email = :email,
                      web = :web 
                  WHERE 
                      customer_id = :customer_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapInsertUpdateCustomerData::map($data);
        return $query_stmt->execute($query_data);
    }
}
