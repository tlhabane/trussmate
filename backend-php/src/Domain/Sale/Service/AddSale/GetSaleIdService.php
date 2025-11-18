<?php

namespace App\Domain\Sale\Service\AddSale;

use App\Domain\Sale\Repository\SaleIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetSaleIdService
{
    private SaleIdExistsRepository $saleIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->saleIdExistsRepository = new SaleIdExistsRepository($connection);
    }

    public function getId(int $length = 32): string
    {
        do {
            $sale_id = Utilities::generateToken($length);
        } while (empty($sale_id) || $this->saleIdExistsRepository->idExists($sale_id));

        return $sale_id;
    }
}
