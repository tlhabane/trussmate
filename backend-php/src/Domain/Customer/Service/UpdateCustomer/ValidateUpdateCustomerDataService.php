<?php

namespace App\Domain\Customer\Service\UpdateCustomer;

use App\Domain\Customer\Service\GetCustomerService;
use App\Domain\Customer\Repository\CustomerEmailExistsRepository;
use App\Domain\Customer\Repository\CustomerTelephoneExistsRepository;
use App\Util\ValidateExistingContactUtility;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateCustomerDataService
{
    private GetCustomerService $getCustomerService;
    private CustomerEmailExistsRepository $emailExistsRepository;
    private CustomerTelephoneExistsRepository $telephoneExistsRepository;
    private ValidateExistingContactUtility $validateExistingContactUtility;

    public function __construct(PDO $connection)
    {
        $this->getCustomerService = new GetCustomerService($connection);
        $this->emailExistsRepository = new CustomerEmailExistsRepository($connection);
        $this->telephoneExistsRepository = new CustomerTelephoneExistsRepository($connection);
        $this->validateExistingContactUtility = new ValidateExistingContactUtility(
            $this->telephoneExistsRepository,
            $this->emailExistsRepository
        );
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $customers = $this->getCustomerService->getCustomer([
            'accountNo' => $data['accountNo'] ?? '',
            'customerId' => $data['customerId'] ?? ''
        ]);
        if (empty($data['customerId']) || count($customers['records']) !== 1) {
            throw new ValidationException('Invalid or missing customer details');
        }

        $fields = [];

        if (empty($data['customerName'])) {
            $fields['customerName'] = 'Invalid customer name provided.';
        }

        foreach ($customers['records'] as $customer) {
            $fields = array_merge($fields, $this->validateExistingContactUtility->validateContact(
                $data,
                $customer
            ));
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
