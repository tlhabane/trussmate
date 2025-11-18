<?php

namespace App\Domain\CustomerAddress\Service;

use App\Domain\CustomerAddress\Repository\UpdateCustomerAddressRepository;
use App\Domain\CustomerAddress\Repository\AddressIdExistsRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateCustomerAddressService
{
    private UpdateCustomerAddressRepository $updateCustomerAddressRepository;
    private AddressIdExistsRepository $addressIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->updateCustomerAddressRepository = new UpdateCustomerAddressRepository($connection);
        $this->addressIdExistsRepository = new AddressIdExistsRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateAddress(array $data): array
    {
        $sanitizedData = SanitizeCustomerAddressDataService::sanitizeData($data);
        $validatedData = ValidateCustomerAddressDataService::validateData($sanitizedData);

        $addressData = MapCustomerAddressDataService::map($validatedData);
        if ($this->addressIdExistsRepository->idExists($addressData->address_id)) {
            if ($this->updateCustomerAddressRepository->updateAddress($addressData)) {
                return [
                    'success' => 'Customer address updated',
                    'id' => $addressData->address_id
                ];
            }

            throw new RuntimeException(
                'Oops! An error occurred while processing you request, please try again.'
            );
        }

        throw new ValidationException('Invalid or missing customer address details');
    }
}
