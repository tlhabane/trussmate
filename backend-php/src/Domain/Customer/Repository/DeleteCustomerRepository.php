<?php

namespace App\Domain\Customer\Repository;

use PDO;

final class DeleteCustomerRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteCustomer(string $customer_id): bool
    {
        $query = "DELETE 
                      c, cp, ca  
                  FROM 
                      customer c 
                  LEFT JOIN 
                      contact cp on c.customer_id = cp.customer_id 
                  LEFT JOIN 
                      customer_address ca on c.customer_id = ca.customer_id 
                  WHERE 
                      c.customer_id = ?";

        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $customer_id);
        return $query_stmt->execute();
    }
}
