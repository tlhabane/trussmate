<?php

namespace App\Domain\Customer\Service\AddCustomer;

use App\Domain\CustomerAddress\Service\AddCustomerAddressService;
use App\Domain\Customer\Repository\AddCustomerRepository;
use App\Domain\Customer\Service\SanitizeCustomerDataService;
use App\Domain\Customer\Service\MapCustomerDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddCustomerService
{
    private GetCustomerIdService $getCustomerIdService;
    private AddCustomerRepository $addCustomerRepository;
    private AddCustomerAddressService $addCustomerAddressService;
    private ValidateAddCustomerDataService $validateAddCustomerDataService;

    public function __construct(PDO $connection)
    {
        $this->getCustomerIdService = new GetCustomerIdService($connection);
        $this->addCustomerRepository = new AddCustomerRepository($connection);
        $this->addCustomerAddressService = new AddCustomerAddressService($connection);
        $this->validateAddCustomerDataService = new ValidateAddCustomerDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addCustomer(array $data): array
    {
        $sanitizedData = SanitizeCustomerDataService::sanitizeData($data);
        $validatedData = $this->validateAddCustomerDataService->validateData($sanitizedData);

        $customerData = MapCustomerDataService::map($validatedData);
        $customerData->account_no = $data['accountNo'];
        $customerData->customer_id = $this->getCustomerIdService->getId(64);

        /* Add customer billing address */
        if (!empty($data['address'])) {
            $this->addCustomerAddressService->addAddress([
                'accountNo' => $data['accountNo'],
                'customerId' => $customerData->customer_id,
                'fullAddress' => $data['address'],
                'billingAddress' => 1
            ]);
        }
        if ($this->addCustomerRepository->addCustomer($customerData)) {
            return [
                'success' => 'Customer detail saved',
                'id' => $customerData->customer_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
