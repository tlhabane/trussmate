<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\SaleDocument\Service\AddSaleDocumentService;
use App\Domain\SaleDocument\Data\DocumentType;
use App\Domain\SaleTask\Data\SaleTaskData;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddSaleTaskDocumentService
{
    private AddSaleDocumentService $addSaleDocumentService;

    public function __construct(PDO $connection)
    {
        $this->addSaleDocumentService = new AddSaleDocumentService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addDocument(array $data, SaleTaskData $taskData): void
    {
        if (isset($data['files']) && is_array($data['files']) && count($data['files']) > 0) {
            $doc_type = isset($data['job']) ? DocumentType::ESTIMATE : DocumentType::OTHER;
            $this->addSaleDocumentService->addDocument([
                'accountNo' => $data['accountNo'],
                'sessionUsername' => $taskData->user_id,
                'saleId' => $taskData->sale_id,
                'saleTaskId' => $taskData->sale_task_id,
                'files' => $data['files'],
                'docType' => $doc_type->value
            ]);
        }
    }
}
