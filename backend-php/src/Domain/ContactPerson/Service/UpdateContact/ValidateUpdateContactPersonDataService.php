<?php

namespace App\Domain\ContactPerson\Service\UpdateContact;

use App\Domain\ContactPerson\Repository\ContactEmailExistsRepository;
use App\Domain\ContactPerson\Repository\ContactTelephoneExistsRepository;
use App\Domain\ContactPerson\Service\GetContactPersonService;
use App\Util\ValidateExistingContactUtility;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateContactPersonDataService
{
    private ValidateExistingContactUtility $validateExistingContactUtility;
    private ContactTelephoneExistsRepository $telephoneExistsRepository;
    private ContactEmailExistsRepository $emailExistsRepository;
    private GetContactPersonService $getContactPersonService;

    public function __construct(PDO $connection)
    {
        $this->telephoneExistsRepository = new ContactTelephoneExistsRepository($connection);
        $this->emailExistsRepository = new ContactEmailExistsRepository($connection);
        $this->validateExistingContactUtility = new ValidateExistingContactUtility(
            $this->telephoneExistsRepository,
            $this->emailExistsRepository
        );
        $this->getContactPersonService = new GetContactPersonService($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $contacts = $this->getContactPersonService->getContact([
            'accountNo' => $data['accountNo'] ?? '',
            'contactId' => $data['contactId'] ?? ''
        ]);
        if (empty($data['contactId']) || count($contacts['records']) !== 1) {
            throw new ValidationException('Invalid or missing contact person details');
        }

        $fields = [];

        if (empty($data['firstName'])) {
            $fields['firstName'] = 'Invalid contact name provided.';
        }
        foreach ($contacts['records'] as $contact) {
            $fields = array_merge($fields, $this->validateExistingContactUtility->validateContact(
                $data,
                $contact
            ));
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
