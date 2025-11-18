<?php

namespace App\Domain\CustomerAddress\Service\DeleteCustomerAddress;

use App\Domain\Sale\Service\GetSaleService;
use App\Exception\ValidationException;
use PDO;

final class ValidateDeleteCustomerAddressDataService
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
        if (empty($data['addressId'])) {
            throw new ValidationException('Invalid or missing customer address details');
        }
        $salesData = [
            'accountNo' => $data['accountNo'],
            'sessionUserRole' => $data['sessionUserRole'],
            'sessionUsername' => $data['sessionUsername']
        ];

        $billingSales = $this->getSaleService->getSale(array_merge($salesData, [
            'billingAddressId' => $data['addressId']
        ]));
        $deliverySales = $this->getSaleService->getSale(array_merge($salesData, [
            'deliveryAddressId' => $data['addressId']
        ]));
        if (count($billingSales['records']) > 0 || count($deliverySales['records']) > 0) {
            throw new ValidationException('Selected address used in recent sales cannot be deleted.');
        }

        return $data;
    }
}
