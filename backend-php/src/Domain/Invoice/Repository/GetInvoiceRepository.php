<?php

namespace App\Domain\Invoice\Repository;

use App\Domain\Invoice\Data\InvoiceData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetInvoiceRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function getInvoice(InvoiceData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      i.sale_task_id, i.invoice_no, i.invoice_no, i.invoice_type, i.created,
                      st.sale_id, t.task_payment, t.task_days, s.sale_no,
                      (sj.total * (t.task_payment / 100)) AS  `invoice_amount`,
                      SUM(tr.transaction_amount) AS `payment_total`,
                      TIMESTAMPADD(DAY, t.task_days, i.created) AS `invoice_due_date`,
                      s.customer_id, IFNULL(c.customer_name, '') AS `customer_name`, 
                      IFNULL(c.email, '') AS 'customer_email', 
                      s.contact_id, IFNULL(cp.first_name, '') AS `first_name`, IFNULL(cp.last_name, '') AS `last_name`,
                      IFNULL(cp.email, '') AS `contact_email`
                  FROM 
                      invoice i 
                  LEFT JOIN
                      sale_task st ON st.sale_task_id = i.sale_task_id 
                  LEFT JOIN
                      task t ON t.task_id = st.task_id
                  LEFT JOIN 
                      sale s on st.sale_id = s.sale_id
                  LEFT JOIN 
                      `transaction` tr ON tr.invoice_no = i.invoice_no 
                  LEFT JOIN 
                      sale_job sj on st.sale_id = sj.sale_id 
                  LEFT JOIN 
                      customer c on s.customer_id = c.customer_id 
                  LEFT JOIN 
                      contact cp on c.customer_id = cp.customer_id
                  WHERE 
                      t.account_no = :account_no";

        $query .= empty($data->sale_task_id) ? "" : " AND i.sale_task_id = :sale_task_id";
        $query .= empty($data->sale_id) ? "" : " AND st.sale_id = :sale_id";
        $query .= empty($data->invoice_type->value) ? "" : " AND i.invoice_type = :invoice_type";
        $query .= empty($data->customer_id) ? "" : " AND c.customer_id = :customer_id";
        $query .= empty($data->contact_id) ? "" : " AND cp.contact_id = :contact_id";

        if (!empty($data->start_date) || !empty($data->end_date)) {
            if (!empty($data->start_date) && !empty($data->end_date)) {
                $query .= " AND (
                    i.created BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                    DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                )";
            } else {
                if (!empty($data->start_date) && empty($data->end_date)) {
                    $query .= " AND (
                        i.created BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:start_date, '%Y-%m-%d 23:59:59')
                    )";
                }
                if (empty($data->start_date) && !empty($data->end_date)) {
                    $query .= " AND (
                        i.created BETWEEN DATE_FORMAT(:end_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                    )";
                }
            }
        }

        if (!empty($data->search)) {
            $query .= " AND (
                LOWER(c.customer_name) LIKE :search OR LOWER(c.tel) LIKE :search OR 
                LOWER(c.alt_tel) LIKE :search OR LOWER(c.email) LIKE :search OR LOWER(c.web) LIKE :search OR 
                LOWER(ca.full_address) LIKE :search OR LOWER(ca.street_address) LIKE :search OR 
                LOWER(ca.suburb) LIKE :search OR LOWER(ca.city) LIKE :search OR LOWER(ca.municipality) LIKE :search OR 
                LOWER(ca.province) LIKE :search OR LOWER(ca.country) LIKE :search OR  
                LOWER(cp.first_name) LIKE :search OR LOWER(cp.last_name) LIKE :search OR LOWER(cp.tel) LIKE :search OR
                LOWER(cp.job_title) LIKE :search OR LOWER(cp.alt_tel) LIKE :search OR LOWER(cp.email) OR 
                LOWER(sj.job_no) LIKE :search
            )";
        }

        $query .= " GROUP BY i.invoice_no ORDER BY i.created DESC";

        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);

        if (!empty($data->sale_task_id)) {
            $query_stmt->bindParam(':sale_task_id', $data->sale_task_id);
        }
        if (!empty($data->sale_id)) {
            $query_stmt->bindParam(':sale_id', $data->sale_id);
        }
        if (!empty($data->invoice_type->value)) {
            $invoice_type = $data->invoice_type->value;
            $query_stmt->bindParam(':invoice_type', $invoice_type);
        }
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
