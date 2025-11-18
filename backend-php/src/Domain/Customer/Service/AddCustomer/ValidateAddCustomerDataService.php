<?php

namespace App\Domain\Customer\Service\AddCustomer;

use App\Domain\Customer\Repository\CustomerEmailExistsRepository;
use App\Domain\Customer\Repository\CustomerTelephoneExistsRepository;
use App\Exception\ValidationException;
use App\Util\ValidateContactUtility;
use PDO;

final class ValidateAddCustomerDataService
{
    private CustomerEmailExistsRepository $emailExistsRepository;
    private CustomerTelephoneExistsRepository $telephoneExistsRepository;
    private ValidateContactUtility $validateContactUtility;

    public function __construct(PDO $connection)
    {
        $this->emailExistsRepository = new CustomerEmailExistsRepository($connection);
        $this->telephoneExistsRepository = new CustomerTelephoneExistsRepository($connection);
        $this->validateContactUtility = new ValidateContactUtility(
            $this->telephoneExistsRepository,
            $this->emailExistsRepository
        );
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $fields = [];

        if (empty($data['customerName'])) {
            $fields['customerName'] = 'Invalid customer name provided.';
        }

        $fields = array_merge($fields, $this->validateContactUtility->validateContact($data));

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
