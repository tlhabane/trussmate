<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\SaleJob\Service\AddUpdateSaleJobService;
use App\Domain\SaleTask\Data\SaleTaskData;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddSaleTaskJobService
{
    private AddUpdateSaleJobService $addUpdateSaleJobService;

    public function __construct(PDO $connection)
    {
        $this->addUpdateSaleJobService = new AddUpdateSaleJobService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addJob(array $data, SaleTaskData $taskData): void
    {
        if (isset($data['job'])) {
            $job = json_decode($data['job'], true);
            $this->addUpdateSaleJobService->addUpdateSale([
                'accountNo' => $data['accountNo'],
                'sessionUsername' => $taskData->user_id,
                'saleId' => $taskData->sale_id,
                'jobNo' => $job['jobNo'] ?? '',
                'jobDescription' => $job['jobDescription'] ?? '',
                'designInfo' => $job['designInfo'] ?? [],
                'lineItems' => $job['lineItems'] ?? [],
                'subtotal' => $job['subtotal'] ?? 0,
                'vat' => $job['vat'] ?? 0,
                'total' => $job['total'] ?? 0
            ]);
        }
    }
}
