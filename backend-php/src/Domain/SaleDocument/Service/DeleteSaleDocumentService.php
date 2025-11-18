<?php

namespace App\Domain\SaleDocument\Service;

use App\Domain\SaleDocument\Repository\DeleteSaleDocumentRepository;
use App\Exception\RuntimeException;
use PDO;

final class DeleteSaleDocumentService
{
    private DeleteSaleDocumentRepository $deleteSaleDocumentRepository;

    public function __construct(PDO $connection)
    {
        $this->deleteSaleDocumentRepository = new DeleteSaleDocumentRepository($connection);
    }

    /**
     * @throws RuntimeException
     */
    public function deleteDocument(array $data): array
    {
        $sanitizedData = SanitizeSaleDocumentDataService::sanitizeData($data);
        if ($this->deleteSaleDocumentRepository->deleteDocument($sanitizedData['docId'])) {
            return [
                'success' => 'Document deleted',
                'id' => $sanitizedData['docId']
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
