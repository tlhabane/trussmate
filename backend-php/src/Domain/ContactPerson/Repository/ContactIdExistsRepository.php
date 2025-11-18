<?php

namespace App\Domain\ContactPerson\Repository;

use PDO;

final class ContactIdExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function idExists(string $contact_id): bool
    {
        $query = "SELECT contact_id FROM contact WHERE TRIM(LOWER(contact_id)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formattedContactId = trim(strtolower($contact_id));
        $query_stmt->bindParam(1, $formattedContactId);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
