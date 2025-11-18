<?php

namespace App\Domain\Customer\Repository;

use App\Contract\TelephoneValidationContract;
use App\Util\Utilities;
use PDO;

final class CustomerTelephoneExistsRepository implements TelephoneValidationContract
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function telExists(string $telephone): bool
    {
        $query = "SELECT 
                      customer_id 
                  FROM 
                      customer 
                  WHERE 
                      REGEXP_REPLACE(tel, '[^0-9]', '') = :tel OR 
                      REGEXP_REPLACE(alt_tel, '[^0-9]', '') = :tel";

        $query_stmt = $this->connection->prepare($query);
        $formattedTelephone = Utilities::removeNonDigits($telephone);
        $query_stmt->bindParam(':tel', $formattedTelephone);
        $query_stmt->execute();

        return $query_stmt->rowCount() > 0;
    }
}
