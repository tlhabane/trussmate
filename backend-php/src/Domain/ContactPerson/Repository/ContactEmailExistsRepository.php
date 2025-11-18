<?php

namespace App\Domain\ContactPerson\Repository;

use App\Contract\EmailValidationContract;
use PDO;

final class ContactEmailExistsRepository implements EmailValidationContract
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function emailExists(string $email_address): bool
    {
        $query = "SELECT contact_id FROM contact WHERE TRIM(LOWER(email)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formattedEmailAddress = trim(strtolower($email_address));
        $query_stmt->bindParam(1, $formattedEmailAddress);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
