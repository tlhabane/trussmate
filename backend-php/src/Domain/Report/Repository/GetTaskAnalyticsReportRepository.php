<?php

namespace App\Domain\Report\Repository;

use App\Domain\Report\Data\ReportData;
use App\Util\SetQueryFilter;
use PDOStatement;
use PDO;

final class GetTaskAnalyticsReportRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getReport(ReportData $data, int $record_start = 0, int $record_limit = 0): PDOStatement
    {
        $query = "SELECT 
                    account_no, customer_id, sale_date, task_month, 
                    SUM(completed)  AS `completed`, (SUM(pending) + SUM(started)) AS `pending`, 
                    SUM(overdue) AS `overdue`,  (SUM(pending) + SUM(started) + SUM(completed)) AS `task_count`
                FROM (
                    SELECT 
                        account_no, customer_id, sale_date, task_month,
                        IF (task_status = 'completed', COUNT(DISTINCT sale_task_id), 0) AS `completed`,
                        IF (task_status = 'pending', COUNT(DISTINCT sale_task_id), 0) AS `pending`,
                        IF (task_status = 'started', COUNT(DISTINCT sale_task_id), 0) AS `started`,
                        IF (task_overdue = 1, COUNT(DISTINCT sale_task_id), 0) AS `overdue`
                    FROM (
                        SELECT 
                            account_no, customer_id, sale_date, sale_task_id, task_status, task_date, 
                            DATE_FORMAT(task_date, '%Y/%m') as `task_month`, task_eta,
                            IF(task_status <> 'completed' AND task_status <> 'cancelled', 
                            TIMESTAMPDIFF(DAY, task_date, NOW()) > task_eta, 0) AS `task_overdue`
                        FROM (
                            SELECT 
                                t.account_no, s.customer_id, s.created as `sale_date`, st.sale_task_id, 
                                ELT(st.task_status, 'pending', 'started', 'cancelled', 'completed') AS `task_status`, 
                                st.created AS `task_date`, st.task_days AS `task_eta`
                            FROM 
                                sale_task st
                            LEFT JOIN
                                task t ON t.task_id = st.task_id 
                            LEFT JOIN 
                                sale s ON s.sale_id = st.sale_id	
                        ) AS `tasks`
                    ) AS `task_statics` 
                    GROUP BY `task_statics`.task_status
                ) AS `task_analytics`
                WHERE 
                    `task_analytics`.account_no = :account_no";

        $query .= empty($data->customer_id) ? "" : " AND `task_analytics`.customer_id = :customer_id";

        if (!empty($data->start_date) || !empty($data->end_date)) {
            if (!empty($data->start_date) && !empty($data->end_date)) {
                $query .= " AND (
                    `task_analytics`.sale_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND
                    DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                )";
            } else {
                if (!empty($data->start_date) && empty($data->end_date)) {
                    $query .= " AND (
                        `task_analytics`.sale_date BETWEEN DATE_FORMAT(:start_date, '%Y-%m-%d 00:00:00') AND
                        DATE_FORMAT(:start_date, '%Y-%m-%d 23:59:59')
                    )";
                }
                if (empty($data->start_date) && !empty($data->end_date)) {
                    $query .= " AND (
                        `task_analytics`.sale_date BETWEEN DATE_FORMAT(:end_date, '%Y-%m-%d 00:00:00') AND
                        DATE_FORMAT(:end_date, '%Y-%m-%d 23:59:59')
                    )";
                }
            }
        }

        $query .= " GROUP BY `task_analytics`.account_no";

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
