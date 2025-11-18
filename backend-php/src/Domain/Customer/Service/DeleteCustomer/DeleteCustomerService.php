<?php

namespace App\Domain\Customer\Service\DeleteCustomer;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\Customer\Repository\DeleteCustomerRepository;
use App\Domain\Customer\Service\SanitizeCustomerDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class DeleteCustomerService
{
    private DeleteCustomerRepository $deleteCustomerRepository;
    private ValidateDeleteCustomerDataService $validateDeleteCustomerDataService;

    public function __construct(PDO $connection)
    {
        $this->deleteCustomerRepository = new DeleteCustomerRepository($connection);
        $this->validateDeleteCustomerDataService = new ValidateDeleteCustomerDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function deleteCustomer(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeCustomerDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $sanitizedData['sessionUserRole'] = $data['sessionUserRole'];
        $sanitizedData['sessionUsername'] = $data['sessionUsername'];
        $validatedData = $this->validateDeleteCustomerDataService->validateData($sanitizedData);

        if ($this->deleteCustomerRepository->deleteCustomer($validatedData['customerId'])) {
            return [
                'success' => 'Customer details deleted',
                'id' => $validatedData['customerId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
