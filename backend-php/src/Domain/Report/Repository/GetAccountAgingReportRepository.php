<?php

namespace App\Domain\Report\Repository;

use App\Domain\Report\Data\ReportData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetAccountAgingReportRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getReport(ReportData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                    account_no, invoice_no, invoice_date, task_payment_type, task_payment, sale_total, payment_total,
                    invoice_amount, invoice_balance, customer_id, customer_name, customer_email, contact_id,
                    first_name, last_name, contact_email, 
                    IF(days_passed >= 181, SUM(invoice_balance) , 0) AS `180_days`,
                    IF(days_passed >= 151 && days_passed <= 180, SUM(invoice_balance) , 0) AS `150_days`,
                    IF(days_passed >= 121 && days_passed <= 150, SUM(invoice_balance) , 0) AS `120_days`,
                    IF(days_passed >= 91 && days_passed <= 120, SUM(invoice_balance) , 0) AS `90_days`,
                    IF(days_passed >= 61 && days_passed <= 90, SUM(invoice_balance) , 0) AS `60_days`,
                    IF(days_passed >= 31 && days_passed <= 60, SUM(invoice_balance) , 0) AS `30_days`,
                    IF(days_passed >= 0 && days_passed <= 30, SUM(invoice_balance) , 0) AS `0_days`,
                    SUM(invoice_balance) AS `total_balance`
                FROM (
                    SELECT 
                        account_no, invoice_no, invoice_date, task_payment_type, task_payment, total AS `sale_total`, 
                        SUM(transaction_amount) as `payment_total`, 
                        SUM(CAST(IFNULL(invoice_amount, 0) AS DECIMAL(19, 2))) AS `invoice_amount`, 
                        (
                            SUM(CAST(IFNULL(invoice_amount, 0) AS DECIMAL(19, 2))) + 
                            SUM(transaction_amount)
                        ) AS `invoice_balance`, customer_id, customer_name, customer_email, contact_id, first_name,
                         last_name, contact_email, TIMESTAMPDIFF(DAY, invoice_date, NOW()) AS `days_passed`
                    FROM (
                        SELECT 
                            s.account_no, i.invoice_no, i.created AS `invoice_date`, t.task_payment_type, 
                            t.task_payment, sj.total, IFNULL(tr.transaction_amount, 0) AS `transaction_amount`,
                            IFNULL(IF(t.task_payment_type = 1, 0, IF(t.task_payment_type = 0, t.task_payment, 
                            sj.total * (t.task_payment / 100))), 0) AS `invoice_amount`,
                            c.customer_id, IFNULL(c.customer_name, '') AS `customer_name`, 
                            IFNULL(c.email, '') AS 'customer_email', IFNULL(s.contact_id, '') AS `contact_id`, 
                            IFNULL(cp.first_name, '') AS `first_name`, IFNULL(cp.last_name, '') AS `last_name`,  
                            IFNULL(cp.email, '') AS `contact_email`
                        FROM `invoice` i  
                        LEFT JOIN `transaction` tr ON i.invoice_no = tr.invoice_no
                        LEFT JOIN `sale_task` st ON st.sale_task_id = i.sale_task_id 
                        LEFT JOIN `task` t ON t.task_id = st.task_id
                        LEFT JOIN `sale_job` sj ON sj.sale_id = st.sale_id
                        LEFT JOIN `sale` s ON s.sale_id = st.sale_id 
                        LEFT JOIN `customer` c on s.customer_id = c.customer_id 
                        LEFT JOIN `contact` cp on s.contact_id = cp.contact_id
                    ) AS `invoices` 
                    GROUP BY `invoices`.invoice_no
                ) AS `transactions`
                WHERE 
                    `transactions`.account_no = :account_no";

        $query .= empty($data->customer_id) ? "" : " AND `transactions`.customer_id = :customer_id";

        if (!empty($data->start_date) || !empty($data->end_date)) {
            if (!empty($data->start_date) && !empty($data->end_date)) {
                $query .= " AND (
                    `transactions`.invoice_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND
                    DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                )";
            } else {
                if (!empty($data->start_date) && empty($data->end_date)) {
                    $query .= " AND (
                        `transactions`.invoice_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND
                        DATE_FORMAT(:start_date, '%Y-%m-%d 23:59:59')
                    )";
                }
                if (empty($data->start_date) && !empty($data->end_date)) {
                    $query .= " AND (
                        `transactions`.invoice_date BETWEEN DATE_FORMAT(:end_date, '%Y-%m-%d 00:00:00') AND
                        DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                    )";
                }
            }
        }

        $query .= " GROUP BY `transactions`.customer_id ORDER BY `transactions`.customer_name";

        $query .= SetQueryFilter::setQueryLimit($record_start, $record_limit);

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(':account_no', $data->account_no);
        if (!empty($data->customer_id)) {
            $query_stmt->bindParam(':customer_id', $data->customer_id);
        }
        $updated_stmt = SetQueryFilter::addQueryFilters($query_stmt, $data, $record_start, $record_limit);
        $updated_stmt->execute();
        return $updated_stmt;
    }
}
