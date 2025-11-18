<?php

namespace App\Domain\Customer\Service\AddCustomer;

use App\Domain\Customer\Repository\CustomerIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetCustomerIdService
{
    private CustomerIdExistsRepository $customerIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->customerIdExistsRepository = new CustomerIdExistsRepository($connection);
    }

    public function getId(int $length): string
    {
        do {
            $customer_id = Utilities::generateToken($length);
        } while (empty($customer_id) || $this->customerIdExistsRepository->idExists($customer_id));

        return $customer_id;
    }
}
