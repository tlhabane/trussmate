<?php

namespace App\Domain\SaleDocument\Service;

use App\Domain\SaleDocument\Data\DocumentType;
use App\Domain\SaleDocument\Repository\AddSaleDocumentRepository;
use App\Domain\SaleDocument\Repository\DeleteAllSaleDocumentsRepository;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use Exception;
use PDO;

final class AddSaleDocumentService
{
    private GetSaleDocumentIdService $getSaleDocumentIdService;
    private AddSaleDocumentRepository $addSaleDocumentRepository;
    private DeleteAllSaleDocumentsRepository $deleteAllSaleDocumentsRepository;

    public function __construct(PDO $connection)
    {
        $this->getSaleDocumentIdService = new GetSaleDocumentIdService($connection);
        $this->addSaleDocumentRepository = new AddSaleDocumentRepository($connection);
        $this->deleteAllSaleDocumentsRepository = new DeleteAllSaleDocumentsRepository($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addDocument(array $data): array
    {
        $sanitizedData = SanitizeSaleDocumentDataService::sanitizeData($data);
        $sanitizedData['files'] = $data['files'] ?? [];
        $validatedData = ValidateAddSaleDocumentDataService::validateData($sanitizedData);
        $documentData = SetSaleDocumentDataService::set($validatedData);
        $documentData->user_id = $data['sessionUsername'];
        $documentData->doc_type = empty($documentData->doc_type->value)
            ? DocumentType::OTHER
            : $documentData->doc_type;

        $docs = [];
        try {
            foreach ($validatedData['files'] as $file) {
                $documentData->doc_id = $this->getSaleDocumentIdService->getId(64);
                $documentData->doc_src = $file['source'];
                $documentData->doc_name = $file['filename'];
                $this->addSaleDocumentRepository->addDocument($documentData);
                $docs[] = $documentData->doc_id;
            }

            return [
                'success' => sprintf("%s documents saved", count($docs)),
                'id' => $docs
            ];
        } catch (Exception $exception) {
            if (count($docs) > 0) {
                $this->deleteAllSaleDocumentsRepository->deleteDocuments($documentData->sale_id);
            }
            throw new RuntimeException($exception->getMessage());
        }
    }
}
