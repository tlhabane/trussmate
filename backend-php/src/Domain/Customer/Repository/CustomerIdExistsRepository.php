<?php

namespace App\Domain\Customer\Repository;

use PDO;

final class CustomerIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $customer_id): bool
    {
        $query = "SELECT customer_id FROM customer WHERE TRIM(LOWER(customer_id)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formattedCustomerId = trim(strtolower($customer_id));
        $query_stmt->bindParam(1, $formattedCustomerId);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
