<?php

namespace App\Domain\SaleTask\Service;

use App\Domain\SaleTask\Repository\GetSaleTaskLogRepository;
use App\Util\Utilities;
use PDO;

final class GetSaleTaskLogService
{
    private GetSaleTaskLogRepository $getSaleTaskLogRepository;

    public function __construct(PDO $connection)
    {
        $this->getSaleTaskLogRepository = new GetSaleTaskLogRepository($connection);
    }

    public function getLog(string $sale_task_id): array
    {
        $sale_task_id = Utilities::sanitizeString($sale_task_id);
        $logs = $this->getSaleTaskLogRepository->getLog($sale_task_id);

        $records = [];
        foreach ($logs as $log) {
            $records[] = [
                'firstName' => Utilities::decodeUTF8($log['first_name']),
                'lastName' => Utilities::decodeUTF8($log['last_name']),
                'taskId' => $log['task_id'],
                'taskName' => Utilities::decodeUTF8($log['task_name']),
                'taskDescription' => Utilities::decodeUTF8($log['task_description']),
                'taskNo' => intval($log['task_no']),
                'taskStatus' => $log['task_status'],
                'taskPayment' => floatval($log['task_payment']),
                'taskPaymentType' => $log['task_payment_type'],
                'taskDays' => intval($log['task_days']),
                'taskFrequency' => intval($log['task_frequency']),
                'taskCompletionDate' => date('c', strtotime($log['task_completion_date'])),
                'comments' => Utilities::decodeUTF8($log['comments']),
                'logDate' => date('c', strtotime($log['created']))
            ];
        }

        return ['records' => $records];
    }
}
