<?php

namespace App\Domain\Sale\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Exception\ValidationException;
use Exception;
use PDO;

final class CreateSaleQuotationService
{
    private GetSaleService $getSaleService;
    private CreateSaleQuotationDocumentService $createSaleQuotationDocumentService;

    public function __construct(PDO $connection)
    {
        $this->getSaleService = new GetSaleService($connection);
        $this->createSaleQuotationDocumentService = new CreateSaleQuotationDocumentService($connection);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws Exception
     */
    public function getQuotation(array $data, string $directory = 'downloads'): array
    {
        $sales = $this->getSaleService->getSale($data);
        foreach ($sales['records'] as $sale) {
            return $this->createSaleQuotationDocumentService->getDocument($data, $sale, $directory);
        }

        throw new ValidationException('Invalid or missing sale details');
    }
}
