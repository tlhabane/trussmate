<?php

namespace App\Domain\SaleDocument\Service;

use App\Domain\SaleDocument\Repository\SaleDocumentIdExistsRepository;
use App\Util\Utilities;
use PDO;

final class GetSaleDocumentIdService
{
    private SaleDocumentIdExistsRepository $saleDocumentIdExistsRepository;

    public function __construct(PDO $connection)
    {
        $this->saleDocumentIdExistsRepository = new SaleDocumentIdExistsRepository($connection);
    }

    public function getId(int $length = 32): string
    {
        do {
            $doc_id = Utilities::generateToken($length);
        } while (empty($doc_id) || $this->saleDocumentIdExistsRepository->idExists($doc_id));

        return $doc_id;
    }
}
