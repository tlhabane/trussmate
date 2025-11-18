<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\SaleTask\Repository\DeleteAllSaleTasksRepository;
use App\Exception\RuntimeException;
use PDO;

final class DeleteAllSaleTasksService
{
    private DeleteAllSaleTasksRepository $deleteAllSaleTasksRepository;

    public function __construct(PDO $connection)
    {
        $this->deleteAllSaleTasksRepository = new DeleteAllSaleTasksRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function deleteTasks(array $data): array
    {
        $sanitizedData = SanitizeSaleTaskDataService::sanitizeData($data);
        if ($this->deleteAllSaleTasksRepository->deleteTask($sanitizedData['saleId'])) {
            return [
                'success' => 'Sales tasks deleted',
                'id' => $sanitizedData['saleId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing your request, please try again.'
        );
    }
}
