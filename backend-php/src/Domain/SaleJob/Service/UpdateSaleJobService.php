<?php

namespace App\Domain\SaleJob\Service;

use App\Domain\SaleJob\Repository\UpdateSaleJobRepository;
use App\Domain\SaleJob\Data\SaleJobData;
use App\Exception\RuntimeException;
use PDO;

final class UpdateSaleJobService
{
    private UpdateSaleJobRepository $updateSaleJobRepository;

    public function __construct(PDO $connection)
    {
        $this->updateSaleJobRepository = new UpdateSaleJobRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function updateSaleJob(SaleJobData $data): array
    {
        if ($this->updateSaleJobRepository->updateSaleJob($data)) {
            return [
                'success' => 'Sale job details updated',
                'id' => $data->sale_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
