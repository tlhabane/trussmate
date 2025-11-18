<?php

namespace App\Domain\Sale\Service\DeleteSale;

use App\Domain\Sale\Data\SaleStatus;
use App\Domain\Sale\Service\GetSaleStatusService;
use App\Domain\Sale\Service\GetSaleService;
use App\Exception\ValidationException;
use PDO;

final class ValidateDeleteSaleDataService
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
        $sales = $this->getSaleService->getSale([
            'accountNo' => $data['accountNo'],
            'saleId' => $data['saleId']
        ]);

        if (empty($data['saleId']) || count($sales['records']) !== 1) {
            throw new ValidationException('Invalid or missing sale details');
        }

        foreach ($sales['records'] as $sale) {
            $sale_status = GetSaleStatusService::getStatus($sale['saleStatus']);
            if ($sale_status === SaleStatus::COMPLETED || $sale_status === SaleStatus::STARTED) {
                throw new ValidationException('Sale is already in progress or has been completed');
            }
        }

        return $data;
    }
}
