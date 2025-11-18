<?php

namespace App\Domain\Invoice\Repository;

use PDOStatement;
use PDO;

final class GetInvoiceByInvoiceNoRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function getInvoice(int $invoice_no): PDOStatement
    {
        $query = "SELECT
                      i.sale_task_id, i.invoice_no, i.invoice_no, i.invoice_type, i.created,
                      st.sale_id, t.task_payment, t.task_payment_type, t.task_days, s.sale_no, sj.total as `job_total`,
                      SUM(IFNULL(tr.transaction_amount, 0)) AS `payment_total`,
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
                      contact cp on s.contact_id = cp.contact_id 
                  WHERE 
                      i.invoice_no = ? 
                  GROUP BY 
                      i.invoice_no";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $invoice_no, PDO::PARAM_INT);
        $query_stmt->execute();

        return $query_stmt;
    }
}
