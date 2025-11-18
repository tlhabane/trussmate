<?php

namespace App\Domain\ContactPerson\Service\AddContact;

use App\Domain\ContactPerson\Repository\ContactIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetContactIdService
{
    private ContactIdExistsRepository $contactIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->contactIdExistsRepository = new ContactIdExistsRepository($connection);
    }

    public function getContactId(int $length): string
    {
        do {
            $contact_id = Utilities::generateToken($length);
        } while (empty($contact_id) || $this->contactIdExistsRepository->idExists($contact_id));

        return $contact_id;
    }
}
