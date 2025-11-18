<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetCustomerRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getCustomer(CustomerData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      c.customer_id, c.customer_type, c.customer_name, c.registration_no, c.vat_no, 
                      c.tel, c.alt_tel, c.email, c.web 
                  FROM 
                      customer c 
                  LEFT JOIN 
                      contact cp ON cp.customer_id = c.customer_id 
                  LEFT JOIN 
                      customer_address ca ON ca.customer_id = c.customer_id 
                  WHERE 
                      c.account_no = :account_no";

        $query.= empty($data->customer_id) ? "" : " AND c.customer_id = :customer_id";
        $query.= empty($data->customer_type->value) ? "" : " AND c.customer_type = :customer_type";

        if (!empty($data->search)) {
            $query.= " AND (
                LOWER(c.customer_name) LIKE :search OR LOWER(c.tel) LIKE :search OR 
                LOWER(c.alt_tel) LIKE :search OR LOWER(c.email) LIKE :search OR LOWER(c.web) LIKE :search OR 
                LOWER(ca.full_address) LIKE :search OR LOWER(ca.street_address) LIKE :search OR 
                LOWER(ca.suburb) LIKE :search OR LOWER(ca.city) LIKE :search OR LOWER(ca.municipality) LIKE :search OR 
                LOWER(ca.province) LIKE :search OR LOWER(ca.country) LIKE :search OR  
                LOWER(cp.first_name) LIKE :search OR LOWER(cp.last_name) LIKE :search OR LOWER(cp.tel) LIKE :search OR
                LOWER(cp.job_title) LIKE :search OR LOWER(cp.alt_tel) LIKE :search OR LOWER(cp.email)
            )";
        }
        $query.= " ORDER BY c.customer_name ASC";
        $query.= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->customer_id)) {
            $query_stmt->bindParam(':customer_id', $data->customer_id);
        }
        if (!empty($data->customer_type->value)) {
            $customer_type = $data->customer_type->value;
            $query_stmt->bindParam(':customer_type', $customer_type);
        }

        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
