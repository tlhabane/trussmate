<?php

namespace App\Domain\SaleJob\Service;

use App\Domain\SaleJob\Repository\SaleJobExistsRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddUpdateSaleJobService
{
    private SaleJobExistsRepository $saleJobExistsRepository;
    private UpdateSaleJobService $updateSaleJobService;
    private AddSaleJobService $addSaleJobService;

    public function __construct(PDO $connection)
    {
        $this->saleJobExistsRepository = new SaleJobExistsRepository($connection);
        $this->updateSaleJobService = new UpdateSaleJobService($connection);
        $this->addSaleJobService = new AddSaleJobService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addUpdateSale(array $data): array
    {
        $sanitizedData = SanitizeSaleJobDataService::sanitizeData($data);
        $validatedData = ValidateSaleJobDataService::validateData($sanitizedData);

        $saleJobData = SetSaleJobDataService::set($validatedData);
        if ($this->saleJobExistsRepository->jobExists($saleJobData->sale_id)) {
            return $this->updateSaleJobService->updateSaleJob($saleJobData);
        }

        return $this->addSaleJobService->addSaleJob($saleJobData);
    }
}
