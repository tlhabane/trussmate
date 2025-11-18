<?php

namespace App\Domain\Transaction\Repository;

use PDOStatement;
use PDO;

final class GetTransactionByIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTransaction(string $transaction_id): PDOStatement
    {
        $query = "SELECT 
                      tr.account_no, tr.user_id, tr.invoice_no, tr.transaction_id, tr.transaction_cancelled, 
                      tr.transaction_amount, tr.transaction_type, tr.transaction_method, tr.transaction_desc, 
                      tr.transaction_date, tr.created AS `posting_date`, st.sale_id, s.sale_no, s.customer_id, 
                      s.contact_id, IFNULL(uai.first_name, '') AS `agent_first_name`, 
                      IFNULL(uai.last_name, '') AS `agent_last_name`, IFNULL(uai.email, '') AS `agent_email`,
                      s.created AS `sale_date`, i.created AS `invoice_date`, sj.total AS `job_total`,
                      IFNULL(c.customer_name, '') AS `customer_name`,
                      IFNULL(c.email, '') AS 'customer_email', s.contact_id, IFNULL(cp.first_name, '') AS `first_name`, 
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
                      sale_job sj on st.sale_id = sj.sale_id         
                  LEFT JOIN 
                      user_account_info uai on tr.user_id = uai.user_id 
                  LEFT JOIN 
                      customer c on s.customer_id = c.customer_id 
                  LEFT JOIN 
                      contact cp on c.customer_id = cp.customer_id
                  WHERE 
                      tr.transaction_id = :transaction_id";

        $statement = $this->connection->prepare($query);
        $statement->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
        $statement->execute();
        return $statement;
    }
}
