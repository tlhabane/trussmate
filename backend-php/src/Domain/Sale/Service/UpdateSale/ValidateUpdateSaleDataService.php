<?php

namespace App\Domain\Sale\Service\UpdateSale;

use App\Domain\Sale\Repository\SaleIdExistsRepository;
use App\Domain\Sale\Service\AddSale\ValidateAddSaleDataService;
use App\Exception\ValidationException;
use PDO;

final class ValidateUpdateSaleDataService
{
    private SaleIdExistsRepository $saleIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->saleIdExistsRepository = new SaleIdExistsRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        if (empty($data['saleId']) || !$this->saleIdExistsRepository->idExists($data['saleId'])) {
            throw new ValidationException('Invalid or missing sale details');
        }

        return ValidateAddSaleDataService::validateData($data);
    }
}
