<?php

namespace App\Domain\Customer\Service\UpdateCustomer;

use App\Domain\Customer\Repository\UpdateCustomerRepository;
use App\Domain\Customer\Service\SanitizeCustomerDataService;
use App\Domain\Customer\Service\MapCustomerDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateCustomerService
{
    private UpdateCustomerRepository $updateCustomerRepository;
    private ValidateUpdateCustomerDataService $validateUpdateCustomerDataService;

    public function __construct(PDO $connection)
    {
        $this->updateCustomerRepository = new UpdateCustomerRepository($connection);
        $this->validateUpdateCustomerDataService = new ValidateUpdateCustomerDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateCustomer(array $data): array
    {
        $sanitizedData = SanitizeCustomerDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $validatedData = $this->validateUpdateCustomerDataService->validateData($sanitizedData);

        $customerData = MapCustomerDataService::map($validatedData);
        if ($this->updateCustomerRepository->updateCustomer($customerData)) {
            return [
                'success' => 'Customer details updated',
                'id' => $customerData->customer_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
