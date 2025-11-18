<?php

namespace App\Domain\CustomerAddress\Service\DeleteCustomerAddress;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\CustomerAddress\Repository\DeleteCustomerAddressRepository;
use App\Domain\CustomerAddress\Service\SanitizeCustomerAddressDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class DeleteCustomerAddressService
{
    private DeleteCustomerAddressRepository $deleteCustomerAddressRepository;
    private ValidateDeleteCustomerAddressDataService $validateDeleteCustomerAddressDataService;

    public function __construct(PDO $connection)
    {
        $this->deleteCustomerAddressRepository = new DeleteCustomerAddressRepository($connection);
        $this->validateDeleteCustomerAddressDataService = new ValidateDeleteCustomerAddressDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function deleteAddress(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeCustomerAddressDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $sanitizedData['sessionUsername'] = $data['sessionUsername'];
        $sanitizedData['sessionUserRole'] = $data['sessionUserRole'];
        $validatedData = $this->validateDeleteCustomerAddressDataService->validateData($sanitizedData);

        if ($this->deleteCustomerAddressRepository->deleteAddress($validatedData['addressId'])) {
            return [
                'success' => 'Customer address deleted',
                'id' => $validatedData['addressId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
