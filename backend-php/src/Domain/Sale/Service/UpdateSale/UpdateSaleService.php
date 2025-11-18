<?php

namespace App\Domain\Sale\Service\UpdateSale;

use App\Domain\Sale\Repository\UpdateSaleRepository;
use App\Domain\Sale\Service\SanitizeSaleDataService;
use App\Domain\Sale\Service\SetSaleDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class UpdateSaleService
{
    private UpdateSaleRepository $updateSaleRepository;
    private ValidateUpdateSaleDataService $validateUpdateSaleDataService;

    public function __construct(PDO $connection)
    {
        $this->updateSaleRepository = new UpdateSaleRepository($connection);
        $this->validateUpdateSaleDataService = new ValidateUpdateSaleDataService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function updateSale(array $data): array
    {
        $sanitizedData = SanitizeSaleDataService::sanitizeData($data);
        $validatedData = $this->validateUpdateSaleDataService->validateData($sanitizedData);

        $salesData = SetSaleDataService::set($validatedData);
        if ($this->updateSaleRepository->updateSale($salesData)) {
            return [
                'success' => 'Sale details updated',
                'id' => $salesData->sale_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
