<?php

namespace App\Domain\CustomerAddress\Repository;

use PDO;

final class DeleteCustomerAddressRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteAddress(string $address_id): bool
    {
        $query = "DELETE FROM customer_address ca WHERE ca.address_id = ?";
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $address_id);
        return $query_stmt->execute();
    }
}
