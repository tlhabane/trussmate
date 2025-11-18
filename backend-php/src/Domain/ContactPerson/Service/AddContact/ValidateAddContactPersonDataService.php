<?php

namespace App\Domain\ContactPerson\Service\AddContact;

use App\Domain\ContactPerson\Repository\ContactEmailExistsRepository;
use App\Domain\ContactPerson\Repository\ContactTelephoneExistsRepository;
use App\Exception\ValidationException;
use App\Util\ValidateContactUtility;
use PDO;

final class ValidateAddContactPersonDataService
{
    private ContactTelephoneExistsRepository $telephoneExistsRepository;
    private ContactEmailExistsRepository $emailExistsRepository;
    private ValidateContactUtility $validateContactUtility;

    public function __construct(PDO $connection)
    {
        $this->telephoneExistsRepository = new ContactTelephoneExistsRepository($connection);
        $this->emailExistsRepository = new ContactEmailExistsRepository($connection);
        $this->validateContactUtility = new ValidateContactUtility(
            $this->telephoneExistsRepository,
            $this->emailExistsRepository
        );
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        if (empty($data['customerId'])) {
            throw new ValidationException('Invalid or missing customer details');
        }
        $fields = [];

        if (empty($data['firstName'])) {
            $fields['firstName'] = 'Invalid contact name provided.';
        }

        $fields = array_merge($fields, $this->validateContactUtility->validateContact($data));

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
