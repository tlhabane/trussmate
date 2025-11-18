<?php

namespace App\Domain\ContactPerson\Service\DeleteContact;

use App\Domain\Sale\Service\GetSaleService;
use App\Exception\ValidationException;
use PDO;

final class ValidateDeleteContactDataService
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
        if (empty($data['contactId'])) {
            throw new ValidationException('Invalid or missing contact person details');
        }

        $sales = $this->getSaleService->getSale($data);
        if (count($sales['records']) > 0) {
            throw new ValidationException('Customers with recent sales cannot be deleted.');
        }

        return $data;
    }
}
