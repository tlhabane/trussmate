<?php

namespace App\Domain\ContactPerson\Repository;

use App\Domain\ContactPerson\Data\ContactPersonData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetContactRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getContact(ContactPersonData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      cp.customer_id, cp.contact_id, cp.first_name, cp.last_name, cp.job_title, cp.tel, 
                      cp.alt_tel, cp.email 
                  FROM 
                      contact cp 
                  LEFT JOIN 
                      customer c on cp.customer_id = c.customer_id
                  WHERE 
                      c.account_no = :account_no";

        $query.= empty($data->customer_id) ? "" : " AND cp.customer_id = :customer_id";
        $query.= empty($data->contact_id) ? "" : " AND cp.contact_id = :contact_id";

        if (!empty($data->search)) {
            $query.= " AND (
                LOWER(c.customer_name) LIKE :search OR LOWER(c.tel) LIKE :search OR 
                LOWER(c.alt_tel) LIKE :search OR LOWER(c.email) LIKE :search OR LOWER(c.web) LIKE :search OR 
                LOWER(cp.first_name) LIKE :search OR LOWER(cp.last_name) LIKE :search OR LOWER(cp.tel) LIKE :search OR
                LOWER(cp.job_title) LIKE :search OR LOWER(cp.alt_tel) LIKE :search OR LOWER(cp.email) 
            )";
        }

        $query.= " ORDER BY cp.last_name, cp.first_name";
        $query.= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->customer_id)) {
            $query_stmt->bindParam(':customer_id', $data->customer_id);
        }
        if (!empty($data->contact_id)) {
            $query_stmt->bindParam(':contact_id', $data->contact_id);
        }
        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
