<?php

namespace App\Domain\Report\Repository;

use App\Domain\Report\Data\ReportData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetAccountBalancesReportRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getReport(ReportData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT
                    account_no, customer_id, contact_id, invoice_month, invoice_date, sale_total, 
                    ABS(SUM(payment_total)) AS `payment_total`, SUM(invoice_balance) AS `invoice_balance`,
                    SUM(overdue_invoice_balance) AS `overdue_invoice_balance`, 
                    AVG(days_overdue) AS `average_days_overdue`
                FROM (
                    SELECT
                        account_no, customer_id, contact_id, invoice_date, invoice_month, 
                        SUM(sale_total) AS `sale_total`, SUM(payment_total) AS `payment_total`, 
                        invoice_overdue_days AS `days_overdue`, 
                        IF(invoice_overdue_days <= 0, SUM(invoice_balance), 0) AS `invoice_balance`,
                        IF(invoice_overdue_days > 0, SUM(invoice_balance), 0) AS `overdue_invoice_balance`
                    FROM (
                        SELECT 
                            account_no, invoice_no, invoice_date, invoice_month, total AS `sale_total`, 
                            SUM(transaction_amount) as `payment_total`, 
                            (SUM(CAST(IFNULL(invoice_amount, 0) AS DECIMAL(19, 2))) + SUM(transaction_amount)) AS `invoice_balance`, customer_id, 
                            contact_id, TIMESTAMPDIFF(DAY, invoice_date, invoice_due_date) AS `invoice_overdue_days`
                        FROM (
                            SELECT 
                                s.account_no, i.invoice_no, i.created AS `invoice_date`, 
                                DATE_FORMAT(i.created, '%Y/%m') AS `invoice_month`, t.task_payment_type,
                                t.task_payment, sj.total, IFNULL(tr.transaction_amount, 0) AS `transaction_amount`,
                                IFNULL(IF(t.task_payment_type = 1, 0, IF(t.task_payment_type = 0, t.task_payment, 
                                    sj.total * (t.task_payment / 100))), 0) AS `invoice_amount`,
                                s.customer_id, IFNULL(s.contact_id, '') AS `contact_id`, 
                                TIMESTAMPADD(DAY, t.task_days, i.created) AS `invoice_due_date`
                            FROM `invoice` i  
                            LEFT JOIN `transaction` tr ON i.invoice_no = tr.invoice_no
                            LEFT JOIN `sale_task` st ON st.sale_task_id = i.sale_task_id 
                            LEFT JOIN `task` t ON t.task_id = st.task_id
                            LEFT JOIN `sale_job` sj ON sj.sale_id = st.sale_id
                            LEFT JOIN `sale` s ON s.sale_id = st.sale_id
                        ) AS `invoices` 
                        GROUP BY `invoices`.invoice_no
                    ) AS `balances`
                    GROUP BY `balances`.invoice_no
                )  AS `account_balances`
                WHERE 
                    `account_balances`.account_no = :account_no";

        $query .= empty($data->customer_id) ? "" : " AND `account_balances`.customer_id = :customer_id";

        if (!empty($data->start_date) || !empty($data->end_date)) {
            if (!empty($data->start_date) && !empty($data->end_date)) {
                $query .= " AND (
                    `account_balances`.invoice_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND
                    DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                )";
            } else {
                if (!empty($data->start_date) && empty($data->end_date)) {
                    $query .= " AND (
                        `account_balances`.invoice_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND
                        DATE_FORMAT(:start_date, '%Y-%m-%d 23:59:59')
                    )";
                }
                if (empty($data->start_date) && !empty($data->end_date)) {
                    $query .= " AND (
                        `account_balances`.invoice_date BETWEEN DATE_FORMAT(:end_date, '%Y-%m-%d 00:00:00') AND
                        DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                    )";
                }
            }
        }

        $query .= " GROUP BY `account_balances`.invoice_month";

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
