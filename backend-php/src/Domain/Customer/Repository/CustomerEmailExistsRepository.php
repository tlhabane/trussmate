<?php

namespace App\Domain\Customer\Repository;

use App\Contract\EmailValidationContract;
use PDO;

final class CustomerEmailExistsRepository implements EmailValidationContract
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function emailExists(string $email_address): bool
    {
        $query = "SELECT customer_id FROM customer WHERE TRIM(LOWER(email)) = ?";
        $query_stmt = $this->connection->prepare($query);

        $formattedEmailAddress = trim(strtolower($email_address));
        $query_stmt->bindParam(1, $formattedEmailAddress);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
