<?php

namespace App\Domain\Sale\Repository;

use App\Domain\Sale\Data\SaleData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetSaleRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getSale(SaleData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      s.sale_id, s.sale_no, s.sale_status, s.customer_id, s.contact_id, s.billing_address_id,
                      s.delivery_address_id, s.workflow_id, s.delivery_required, s.labour_required, s.created, 
                      w.workflow_name, c.customer_name, cp.first_name, cp.last_name,
                      IF(ISNULL(cp.tel) OR TRIM(cp.tel) = '', c.tel, cp.tel) AS `tel`,
                      IF(ISNULL(cp.alt_tel) OR TRIM(cp.alt_tel) = '', c.alt_tel, cp.alt_tel) AS `alt_tel`,
                      IF(ISNULL(cp.email) OR TRIM(cp.email) = '', c.email, cp.email) AS `email`
                  FROM 
                      sale s 
                  LEFT JOIN 
                      workflow w ON w.workflow_id = s.workflow_id 
                  LEFT JOIN 
                      customer c ON s.customer_id = c.customer_id 
                  LEFT JOIN 
                      contact cp ON s.contact_id = cp.contact_id 
                  LEFT JOIN 
                      customer_address ca ON s.customer_id = ca.customer_id   
                  WHERE 
                      s.account_no = :account_no";

        $query .= empty($data->customer_id) ? "" : " AND c.customer_id = :customer_id";
        $query .= empty($data->contact_id) ? "" : " AND cp.contact_id = :contact_id";
        $query .= empty($data->sale_id) ? "" : " AND s.sale_id = :sale_id";
        $query .= empty($data->sale_status->value) ? "" : " AND s.sale_status = :sale_status";
        $query .= empty($data->billing_address_id) ? "" : " AND s.billing_address_id = :billing_address_id";
        $query .= empty($data->delivery_address_id) ? "" : " AND s.delivery_address_id = :delivery_address_id";
        if (!empty($data->start_date) || !empty($data->end_date)) {
            if (!empty($data->start_date) && !empty($data->end_date)) {
                $query .= " AND (
                    s.created BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                    DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                )";
            } else {
                if (!empty($data->start_date) && empty($data->end_date)) {
                    $query .= " AND (
                        s.created BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:start_date, '%Y-%m-%d 23:59:59')
                    )";
                }
                if (empty($data->start_date) && !empty($data->end_date)) {
                    $query .= " AND (
                        s.created BETWEEN DATE_FORMAT(:end_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                    )";
                }
            }
        }

        if (!empty($data->search)) {
            $query .= " AND (
                LOWER(c.customer_name) LIKE :search OR LOWER(c.tel) LIKE :search OR 
                LOWER(c.alt_tel) LIKE :search OR LOWER(c.email) LIKE :search OR LOWER(c.web) LIKE :search OR 
                LOWER(cp.first_name) LIKE :search OR LOWER(cp.last_name) LIKE :search OR LOWER(cp.tel) LIKE :search OR
                LOWER(cp.job_title) LIKE :search OR LOWER(cp.alt_tel) LIKE :search OR LOWER(cp.email) OR 
                LOWER(ca.full_address) LIKE :search OR LOWER(ca.street_address) LIKE :search OR 
                LOWER(ca.suburb) LIKE :search OR LOWER(ca.city) LIKE :search OR LOWER(ca.municipality) LIKE :search OR 
                LOWER(ca.province) LIKE :search OR LOWER(ca.country) LIKE :search
            )";
        }

        $query .= " ORDER BY s.created DESC";
        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->customer_id)) {
            $query_stmt->bindParam(':customer_id', $data->customer_id);
        }
        if (!empty($data->contact_id)) {
            $query_stmt->bindParam(':contact_id', $data->contact_id);
        }
        if (!empty($data->sale_id)) {
            $query_stmt->bindParam(':sale_id', $data->sale_id);
        }
        if (!empty($data->sale_status->value)) {
            $sale_status = $data->sale_status->value;
            $query_stmt->bindParam(':sale_status', $sale_status);
        }
        if (!empty($data->billing_address_id)) {
            $query_stmt->bindParam(':billing_address_id', $data->billing_address_id);
        }
        if (!empty($data->delivery_address_id)) {
            $query_stmt->bindParam(':delivery_address_id', $data->delivery_address_id);
        }
        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
