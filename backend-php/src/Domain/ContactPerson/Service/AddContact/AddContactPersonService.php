<?php

namespace App\Domain\ContactPerson\Service\AddContact;

use App\Domain\ContactPerson\Repository\AddContactRepository;
use App\Domain\ContactPerson\Service\SanitizeContactPersonDataService;
use App\Domain\ContactPerson\Service\MapContactPersonDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddContactPersonService
{
    private AddContactRepository $addContactRepository;
    private GetContactIdService $getContactIdService;
    private ValidateAddContactPersonDataService $validateAddContactPersonDataService;

    public function __construct(PDO $connection)
    {
        $this->addContactRepository = new AddContactRepository($connection);
        $this->getContactIdService = new GetContactIdService($connection);
        $this->validateAddContactPersonDataService = new ValidateAddContactPersonDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addContact(array $data): array
    {
        $sanitizedData = SanitizeContactPersonDataService::sanitizeData($data);
        $validatedData = $this->validateAddContactPersonDataService->validateData($sanitizedData);

        $contact = MapContactPersonDataService::map($validatedData);
        $contact->contact_id = $this->getContactIdService->getContactId(64);
        $contact->account_no = $data['accountNo'];

        if ($this->addContactRepository->addContact($contact)) {
            return [
                'success' => 'Contact person details saved',
                'id' => $contact->contact_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
