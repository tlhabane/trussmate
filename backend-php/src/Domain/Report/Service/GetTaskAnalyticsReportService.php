<?php

namespace App\Domain\Report\Service;

use App\Domain\Report\Repository\GetTaskAnalyticsReportRepository;
use App\Exception\ValidationException;
use PDO;

final class GetTaskAnalyticsReportService
{
    private GetTaskAnalyticsReportRepository $getTaskAnalyticsReportRepository;

    public function __construct(PDO $connection)
    {
        $this->getTaskAnalyticsReportRepository = new GetTaskAnalyticsReportRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getReport(array $data): array
    {
        $sanitizedData = SanitizeReportDataService::sanitize($data);
        $reportData = SetReportDataService::set($sanitizedData);
        $reportData->account_no = $data['accountNo'];
        $reports = $this->getTaskAnalyticsReportRepository->getReport($reportData);

        $records = [];
        foreach ($reports as $report) {
            $records[] = [
                'customerId' => $report['customer_id'],
                'saleDate' => date('c', strtotime($report['sale_date'])),
                'taskCount' => intval($report['task_count']),
                'taskMonth' => $report['task_month'],
                'completed' => intval($report['completed']),
                'pending' => intval($report['pending']),
                'overdue' => intval($report['overdue']),
            ];
        }

        return [
            'records' => $records,
        ];
    }
}
