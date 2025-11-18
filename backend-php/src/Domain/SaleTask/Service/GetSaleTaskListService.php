<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\SaleDocument\Service\GetSaleDocumentService;
use PDO;

final class GetSaleTaskListService
{
    private GetSaleDocumentService $getSaleDocumentService;
    private GetSaleTaskService $getSaleTaskService;

    public function __construct(PDO $connection)
    {
        $this->getSaleDocumentService = new GetSaleDocumentService($connection);
        $this->getSaleTaskService = new GetSaleTaskService($connection);
    }

    public function getTask(array $data): array
    {
        $records = [];
        $tasks = $this->getSaleTaskService->getTask($data);

        foreach ($tasks['records'] as $task) {
            $documents = $this->getSaleDocumentService->getSaleDocument([
                'accountNo' => $data['accountNo'],
                'saleId' => $task['saleId'],
                /*'saleTaskId' => $task['saleTaskId']*/
            ]);

            $records[] = array_merge($task, ['documents' => $documents['records'] ?? []]);
        }

        if (isset($tasks['pagination'])) {
            return ['records' => $records, 'pagination' => $tasks['pagination']];
        }

        return ['records' => $records];
    }
}
