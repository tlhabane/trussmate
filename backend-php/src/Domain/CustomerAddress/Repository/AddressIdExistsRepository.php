<?php

namespace App\Domain\CustomerAddress\Repository;

use PDO;

final class AddressIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $address_id): bool
    {
        $query = "SELECT address_id FROM customer_address WHERE TRIM(LOWER(address_id)) = ?";
        $query_stmt = $this->connection->prepare($query);
        $formattedAddressId = trim(strtolower($address_id));
        $query_stmt->bindParam(1, $formattedAddressId);
        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
