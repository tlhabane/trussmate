<?php

namespace App\Domain\ContactPerson\Repository;

use App\Domain\ContactPerson\Data\MapInsertUpdateContactPersonData;
use App\Domain\ContactPerson\Data\ContactPersonData;
use PDO;

final class UpdateContactRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function updateContact(ContactPersonData $data): bool {
        $query = "UPDATE contact SET  
                      first_name = :first_name,
                      last_name = :last_name,
                      job_title = :job_title,
                      tel = :tel,
                      alt_tel = :alt_tel,
                      email = :email 
                  WHERE 
                      contact_id = :contact_id";

        $query_stmt = $this->connection->prepare($query);
        $query_data = MapInsertUpdateContactPersonData::map($data);
        return $query_stmt->execute($query_data);
    }
}
