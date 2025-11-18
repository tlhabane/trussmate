<?php

namespace App\Domain\ContactPerson\Service\UpdateContact;

use App\Domain\ContactPerson\Repository\UpdateContactRepository;
use App\Domain\ContactPerson\Service\SanitizeContactPersonDataService;
use App\Domain\ContactPerson\Service\MapContactPersonDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateContactPersonService
{
    private UpdateContactRepository $updateContactRepository;
    private ValidateUpdateContactPersonDataService $validateUpdateContactPersonDataService;

    public function __construct(PDO $connection)
    {
        $this->updateContactRepository = new UpdateContactRepository($connection);
        $this->validateUpdateContactPersonDataService = new ValidateUpdateContactPersonDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateContact(array $data): array
    {
        $sanitizedData = SanitizeContactPersonDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateUpdateContactPersonDataService->validateData($sanitizedData);
        $customerData = MapContactPersonDataService::map($validatedData);
        if ($this->updateContactRepository->updateContact($customerData)) {
            return [
                'success' => 'Contact person details updated',
                'id' => $customerData->contact_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
