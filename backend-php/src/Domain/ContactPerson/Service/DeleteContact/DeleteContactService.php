<?php

namespace App\Domain\ContactPerson\Service\DeleteContact;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\ContactPerson\Repository\DeleteContactRepository;
use App\Domain\ContactPerson\Service\SanitizeContactPersonDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class DeleteContactService
{
    private DeleteContactRepository $deleteContactRepository;
    private ValidateDeleteContactDataService $validateDeleteContactDataService;

    public function __construct(PDO $connection)
    {
        $this->deleteContactRepository = new DeleteContactRepository($connection);
        $this->validateDeleteContactDataService = new ValidateDeleteContactDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function deleteContact(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeContactPersonDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $sanitizedData['sessionUserRole'] = $data['sessionUserRole'];
        $sanitizedData['sessionUsername'] = $data['sessionUsername'];
        $validatedData = $this->validateDeleteContactDataService->validateData($sanitizedData);

        if ($this->deleteContactRepository->deleteContact($validatedData['contactId'])) {
            return [
                'success' => 'Customer details deleted',
                'id' => $validatedData['contactId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
