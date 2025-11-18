<?php

namespace App\Domain\ContactPerson\Repository;

use PDO;

final class DeleteContactRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function deleteContact(string $contact_id): bool
    {
        $query = "DELETE FROM contact WHERE contact_id = ?";
        $query_stmt = $this->connection->prepare($query);
        $query_stmt->bindParam(1, $contact_id);
        return $query_stmt->execute();
    }
}
