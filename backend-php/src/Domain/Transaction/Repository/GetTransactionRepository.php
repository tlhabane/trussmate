<?php

namespace App\Domain\Transaction\Repository;

use App\Domain\Transaction\Data\TransactionData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetTransactionRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTransaction(TransactionData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                      account_no, user_id, invoice_no, transaction_id, transaction_cancelled, transaction_amount, 
                      transaction_id, transaction_type, transaction_date, transaction_desc, transaction_method, 
                      `posting_date`, sale_id, sale_no, customer_id, customer_name, customer_email, contact_id, 
                      first_name, last_name, contact_email
                  FROM
                  (
                      SELECT 
                          tr.account_no, tr.user_id, tr.invoice_no, tr.transaction_id, tr.transaction_cancelled, 
                          tr.transaction_amount, tr.transaction_type, tr.transaction_date, tr.transaction_desc, 
                          tr.transaction_method,  tr.created AS `posting_date`, st.sale_id, s.sale_no, s.customer_id,
                          IFNULL(c.customer_name, '') AS `customer_name`, IFNULL(c.email, '') AS 'customer_email',
                          s.contact_id, IFNULL(cp.first_name, '') AS `first_name`, 
                          IFNULL(cp.last_name, '') AS `last_name`, IFNULL(cp.email, '') AS `contact_email`
                      FROM 
                          `transaction` tr 
                      LEFT JOIN 
                          invoice i ON i.invoice_no = tr.invoice_no 
                      LEFT JOIN
                          sale_task st ON st.sale_task_id = i.sale_task_id 
                      LEFT JOIN 
                          sale s on st.sale_id = s.sale_id        
                      LEFT JOIN 
                      customer c on s.customer_id = c.customer_id 
                      LEFT JOIN 
                          contact cp on s.contact_id = cp.contact_id
                      
                      UNION ALL 
                      
                      SELECT 
                          t.account_no, wt.assigned_to AS `user_id`, i.invoice_no, i.sale_task_id as `transaction_id`, 
                          1 AS `transaction_cancelled`, (sj.total * (t.task_payment / 100)) AS  `transaction_amount`, 
                          i.invoice_type AS `transaction_type`, i.created AS `transaction_date`, 
                          '' AS transaction_desc, 'other' AS transaction_method,
                          TIMESTAMPADD(DAY, t.task_days, i.created) AS `posting_date`, st.sale_id, s.sale_no, 
                          s.customer_id, IFNULL(c.customer_name, '') AS `customer_name`, 
                          IFNULL(c.email, '') AS 'customer_email', s.contact_id, 
                          IFNULL(cp.first_name, '') AS `first_name`, IFNULL(cp.last_name, '') AS `last_name`, 
                          IFNULL(cp.email, '') AS `contact_email`
                      FROM 
                          invoice i
                      LEFT JOIN
                          sale_task st ON st.sale_task_id = i.sale_task_id 
                      LEFT JOIN
                          task t ON t.task_id = st.task_id
                      LEFT JOIN 
                          workflow_task wt on wt.task_id = st.task_id    
                      LEFT JOIN 
                          sale_job sj on st.sale_id = sj.sale_id 
                      LEFT JOIN 
                          sale s on st.sale_id = s.sale_id        
                      LEFT JOIN 
                          customer c on s.customer_id = c.customer_id 
                      LEFT JOIN 
                          contact cp on s.contact_id = cp.contact_id
                  ) AS `transactions` 
                  WHERE 
                      `transactions`.account_no = :account_no";

        $query .= empty($data->invoice_no) ? "" : " AND `transactions`.invoice_no = :invoice_no";
        $query .= empty($data->sale_id) ? "" : " AND `transactions`.sale_id = :sale_id";
        $query .= empty($data->transaction_type->value)
            ? ""
            : " AND `transactions`.transaction_type = :transaction_type";
        $query .= empty($data->customer_id) ? "" : " AND `transactions`.customer_id = :customer_id";
        $query .= empty($data->contact_id) ? "" : " AND `transactions`.contact_id = :contact_id";

        if (!empty($data->start_date) || !empty($data->end_date)) {
            if (!empty($data->start_date) && !empty($data->end_date)) {
                $query .= " AND (
                    `transactions`.transaction_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                    DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                )";
            } else {
                if (!empty($data->start_date) && empty($data->end_date)) {
                    $query .= " AND (
                        `transactions`.transaction_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:start_date, '%Y-%m-%d 23:59:59')
                    )";
                }
                if (empty($data->start_date) && !empty($data->end_date)) {
                    $query .= " AND (
                        `transactions`.transaction_date BETWEEN DATE_FORMAT(:end_date, '%Y-%m-%d 00:00:00') AND 
                        DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                    )";
                }
            }
        }

        if (!empty($data->search)) {
            $query .= " AND (
                 LOWER(`transactions`.customer_name) LIKE :search OR `transactions`.customer_email LIKE :search OR
                 LOWER(`transactions`.first_name) LIKE :search OR LOWER(`transactions`.last_name) LIKE :search OR
                 `transactions`.contact_email LIKE :search OR `transactions`.transaction_type LIKE :search
             )";
        }

        $query .= " ORDER BY `transactions`.transaction_date DESC";

        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->invoice_no)) {
            $query_stmt->bindParam(':invoice_no', $data->invoice_no);
        }
        if (!empty($data->sale_id)) {
            $query_stmt->bindParam(':sale_id', $data->sale_id);
        }
        if (!empty($data->transaction_type->value)) {
            $transaction_type = $data->transaction_type->value;
            $query_stmt->bindParam(':transaction_type', $transaction_type);
        }
        if (!empty($data->customer_id)) {
            $query_stmt->bindParam(':customer_id', $data->customer_id);
        }
        if (!empty($data->contact_id)) {
            $query_stmt->bindParam(':contact_id', $data->contact_id);
        }
        if (!empty($data->search)) {
            $data->search = strtolower(trim($data->search));
            $data->search = str_replace('%20', ' ', $data->search);
            $data->search = "%{$data->search}%";
            $query_stmt->bindParam(':search', $data->search);
        }
        if ($record_start > 0) {
            $query_stmt->bindParam(':record_start', $record_start, PDO::PARAM_INT);
        }
        if ($record_limit > 0) {
            $query_stmt->bindParam(':record_limit', $record_limit, PDO::PARAM_INT);
        }

        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
