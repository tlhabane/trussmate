<?php

namespace App\Domain\SaleJob\Service;

use App\Domain\SaleJob\Repository\DeleteAllSaleJobsRepository;
use App\Exception\RuntimeException;
use PDO;

final class DeleteAllSaleJobsService
{
    private DeleteAllSaleJobsRepository $deleteAllSaleJobsRepository;

    public function __construct(PDO $connection)
    {
        $this->deleteAllSaleJobsRepository = new DeleteAllSaleJobsRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function deleteSaleJob(array $data): array
    {
        $sanitizedData = SanitizeSaleJobDataService::sanitizeData($data);
        if ($this->deleteAllSaleJobsRepository->deleteSaleJob($sanitizedData['saleId'])) {
            return [
                'success' => 'Sale jobs deleted',
                'id' => $sanitizedData['saleId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
