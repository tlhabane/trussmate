<?php

namespace App\Domain\Sale\Service\AddSale;

use App\Domain\SaleDocument\Data\DocumentType;
use App\Domain\SaleDocument\Service\AddSaleDocumentService;
use App\Domain\Sale\Repository\AddSaleRepository;
use App\Domain\Sale\Service\SanitizeSaleDataService;
use App\Domain\Sale\Service\SetSaleDataService;
use App\Exception\ValidationException;
use App\Exception\RuntimeException;
use PDO;

final class AddSaleService
{
    private AddSaleDocumentService $addSaleDocumentService;
    private AddSaleWorkflowTasksService $addSaleWorkflowTasksService;
    private AddSaleRepository $addSaleRepository;
    private GetSaleIdService $getSaleIdService;

    public function __construct(PDO $connection)
    {
        $this->addSaleDocumentService = new AddSaleDocumentService($connection);
        $this->addSaleWorkflowTasksService = new AddSaleWorkflowTasksService($connection);
        $this->addSaleRepository = new AddSaleRepository($connection);
        $this->getSaleIdService = new GetSaleIdService($connection);
    }

    /**
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function addSale(array $data): array
    {
        $sanitizedData = SanitizeSaleDataService::sanitizeData($data);
        $validatedData = ValidateAddSaleDataService::validateData($sanitizedData);

        $salesData = SetSaleDataService::set($validatedData);
        $salesData->account_no = $data['accountNo'];
        $salesData->sale_id = $this->getSaleIdService->getId(64);

        /* Add floor plans */
        $this->addSaleDocumentService->addDocument([
            'accountNo' => $salesData->account_no,
            'sessionUsername' => $data['sessionUsername'],
            'saleId' => $salesData->sale_id,
            'files' => $data['files'],
            'docType' => DocumentType::FLOOR_PLAN->value
        ]);

        /* Queue(add) sale tasks based on the selected workflow */
        $this->addSaleWorkflowTasksService->addTask([
            'accountNo' => $salesData->account_no,
            'sessionUsername' => $data['sessionUsername'],
            'workflowId' => $salesData->workflow_id,
            'saleId' => $salesData->sale_id
        ]);

        /* Finally: add the sale */
        if ($this->addSaleRepository->addSale($salesData)) {
            return [
                'success' => 'Sale details saved',
                'id' => $salesData->sale_id
            ];
        }

        throw new RuntimeException(
            'Oops! An error occurred while processing you request, please try again.'
        );
    }
}
