<?php

namespace App\Domain\ContactPerson\Repository;

use App\Domain\ContactPerson\Data\MapInsertUpdateContactPersonData;
use App\Domain\ContactPerson\Data\ContactPersonData;
use PDO;

final class AddContactRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function addContact(ContactPersonData $data): bool {
        $query = "INSERT INTO contact SET 
                  customer_id = :customer_id,      
                  contact_id = :contact_id,
                  first_name = :first_name,
                  last_name = :last_name,
                  job_title = :job_title,
                  tel = :tel,
                  alt_tel = :alt_tel,
                  email = :email";

        $query_stmt = $this->connection->prepare($query);
        $query_data = array_merge(MapInsertUpdateContactPersonData::map($data), [
            'customer_id' => $data->customer_id
        ]);
        return $query_stmt->execute($query_data);
    }
}
