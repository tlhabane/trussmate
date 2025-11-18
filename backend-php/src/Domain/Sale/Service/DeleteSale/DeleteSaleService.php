<?php

namespace App\Domain\Sale\Service\DeleteSale;

use App\Domain\User\Service\ValidateSuperAdminPrivilegeService;
use App\Domain\Sale\Service\SanitizeSaleDataService;
use App\Domain\SaleTask\Service\DeleteAllSaleTasksService;
use App\Domain\SaleJob\Service\DeleteAllSaleJobsService;
use App\Domain\Sale\Repository\DeleteSaleRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class DeleteSaleService
{
    private ValidateDeleteSaleDataService $validateDeleteSaleDataService;
    private DeleteAllSaleTasksService $deleteAllSaleTasksService;
    private DeleteAllSaleJobsService $deleteAllSaleJobsService;
    private DeleteSaleRepository $deleteSaleRepository;

    public function __construct(PDO $connection)
    {
        $this->validateDeleteSaleDataService = new ValidateDeleteSaleDataService($connection);
        $this->deleteAllSaleTasksService = new DeleteAllSaleTasksService($connection);
        $this->deleteAllSaleJobsService = new DeleteAllSaleJobsService($connection);
        $this->deleteSaleRepository = new DeleteSaleRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function deleteSale(array $data): array
    {
        /* Validate user privileges */
        ValidateSuperAdminPrivilegeService::validate($data['sessionUserRole']);
        $sanitizedData = SanitizeSaleDataService::sanitizeData($data);
        $sanitizedData['accountNo'] = $data['accountNo'];
        $sanitizedData['sessionUsername'] = $data['sessionUsername'];
        $sanitizedData['sessionUserRole'] = $data['sessionUserRole'];
        $validatedData = $this->validateDeleteSaleDataService->validateData($sanitizedData);

        $this->deleteAllSaleJobsService->deleteSaleJob($validatedData);
        $this->deleteAllSaleTasksService->deleteTasks($validatedData);
        if ($this->deleteSaleRepository->deleteSale($validatedData['saleId'])) {
            return [
                'success' => 'Sale record deleted',
                'id' => $validatedData['saleId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
