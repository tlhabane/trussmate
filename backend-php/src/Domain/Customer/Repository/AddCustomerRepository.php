<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use App\Domain\Customer\Data\MapInsertUpdateCustomerData;
use PDO;

final class AddCustomerRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addCustomer(CustomerData $data): bool
    {
        $query = "INSERT INTO customer SET 
                  account_no = :account_no,
                  customer_id = :customer_id,
                  customer_type = :customer_type,
                  customer_name = :customer_name,
                  registration_no = :registration_no,
                  vat_no = :vat_no,
                  tel = :tel,
                  alt_tel = :alt_tel,
                  email = :email,
                  web = :web";

        $query_stmt = $this->connection->prepare($query);
        $query_data = array_merge(MapInsertUpdateCustomerData::map($data), [
            'account_no' => $data->account_no
        ]);
        return $query_stmt->execute($query_data);
    }
}
