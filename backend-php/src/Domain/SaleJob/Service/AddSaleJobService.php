<?php

namespace App\Domain\SaleJob\Service;

use App\Domain\SaleJob\Repository\AddSaleJobRepository;
use App\Domain\SaleJob\Data\SaleJobData;
use App\Exception\RuntimeException;
use PDO;

final class AddSaleJobService
{
    private AddSaleJobRepository $addSaleJobRepository;

    public function __construct(PDO $connection)
    {
        $this->addSaleJobRepository = new AddSaleJobRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function addSaleJob(SaleJobData $data): array
    {
        if ($this->addSaleJobRepository->addSaleJob($data)) {
            return [
                'success' => 'Sale job details saved',
                'id' => $data->sale_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
