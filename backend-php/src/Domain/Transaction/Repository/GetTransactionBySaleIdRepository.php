<?php

namespace App\Domain\Transaction\Repository;

use PDOStatement;
use PDO;

final class GetTransactionBySaleIdRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTransaction(string $sale_id): PDOStatement
    {
        $query = "SELECT 
                      tr.account_no, tr.user_id, tr.invoice_no, tr.transaction_id, tr.transaction_cancelled, 
                      tr.transaction_amount, tr.transaction_type, tr.transaction_method, tr.transaction_desc, 
                      tr.transaction_date, tr.created AS `posting_date`, IFNULL(c.customer_name, '') AS `customer_name`,
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
                      customer c on s.customer_id = c.customer_id 
                  LEFT JOIN 
                      contact cp on s.contact_id = cp.contact_id
                  WHERE 
                      st.sale_id = :sale_id";

        $statement = $this->connection->prepare($query);
        $statement->bindParam(':sale_id', $sale_id);
        $statement->execute();
        return $statement;
    }
}
