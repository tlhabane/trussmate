<?php

namespace App\Domain\SaleDocument\Service;

use App\Domain\SaleDocument\Repository\DeleteAllSaleDocumentsRepository;
use App\Exception\RuntimeException;
use PDO;

final class DeleteAllSaleDocumentsService
{
    private DeleteAllSaleDocumentsRepository $deleteAllSaleDocumentsRepository;

    public function __construct(PDO $connection)
    {
        $this->deleteAllSaleDocumentsRepository = new DeleteAllSaleDocumentsRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function deleteDocuments(array $data): array
    {
        $sanitizedData = SanitizeSaleDocumentDataService::sanitizeData($data);
        if ($this->deleteAllSaleDocumentsRepository->deleteDocuments($sanitizedData['saleId'])) {
            return [
                'success' => 'Documents deleted',
                'id' => $sanitizedData['saleId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
