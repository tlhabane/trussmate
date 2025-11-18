<?php

namespace App\Domain\CustomerAddress\Service;

use App\Domain\CustomerAddress\Repository\AddCustomerAddressRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddCustomerAddressService
{
    private AddCustomerAddressRepository $addCustomerAddressRepository;
    private GetAddressIdService $getAddressIdService;

    public function __construct(PDO $connection)
    {
        $this->addCustomerAddressRepository = new AddCustomerAddressRepository($connection);
        $this->getAddressIdService = new GetAddressIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addAddress(array $data): array
    {
        $sanitizedData = SanitizeCustomerAddressDataService::sanitizeData($data);
        $validatedData = ValidateCustomerAddressDataService::validateData($sanitizedData);

        $addressData = MapCustomerAddressDataService::map($validatedData);
        $addressData->address_id = $this->getAddressIdService->getAddressId(64);
        if ($this->addCustomerAddressRepository->addAddress($addressData)) {
            return [
                'success' => 'Customer address details saved',
                'id' => $addressData->address_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
