<?php

namespace App\Domain\SaleTask\Service\AddSaleTask;

use App\Domain\SaleTask\Repository\SaleTaskIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetSaleTaskIdService
{
    private SaleTaskIdExistsRepository $saleTaskIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->saleTaskIdExistsRepository = new SaleTaskIdExistsRepository($connection);
    }

    public function getId(int $length = 32): string
    {
        do {
            $sale_task_id = Utilities::generateToken($length);
        } while (empty($sale_task_id) || $this->saleTaskIdExistsRepository->idExists($sale_task_id));

        return $sale_task_id;
    }
}
