<?php

namespace App\Domain\User\Repository;

use App\Util\Utilities;
use PDO;

final class UserPhoneNumberExistsRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function numberExists(string $tel): bool
    {
        $query = "SELECT 
                      tel, alt_tel 
                  FROM 
                      user_account_info 
                  WHERE 
                      REGEXP_REPLACE(tel, '[^0-9]', '') = :tel OR 
                      REGEXP_REPLACE(alt_tel, '[^0-9]', '') = :tel";

        $query_stmt = $this->connection->prepare($query);
        $formattedPhoneNo = Utilities::removeNonDigits($tel);
        $query_stmt->bindParam(':tel', $formattedPhoneNo);

        $query_stmt->execute();
        return $query_stmt->rowCount() > 0;
    }
}
