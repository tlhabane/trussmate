<?php

namespace App\Domain\Customer\Service\DeleteCustomer;

use App\Domain\Sale\Service\GetSaleService;
use App\Exception\ValidationException;
use PDO;

final class ValidateDeleteCustomerDataService
{
    private GetSaleService $getSaleService;

    public function __construct(PDO $connection)
    {
        $this->getSaleService = new GetSaleService($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        if (empty($data['customerId'])) {
            throw new ValidationException('Invalid or missing customer details');
        }

        $sales = $this->getSaleService->getSale($data);
        if (count($sales['records']) > 0) {
            throw new ValidationException('Customers with recent sales cannot be deleted.');
        }

        return $data;
    }
}
