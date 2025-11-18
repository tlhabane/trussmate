<?php

namespace App\Domain\Report\Service;

use App\Domain\Report\Repository\GetAccountAgingReportRepository;
use App\Exception\ValidationException;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetAccountAgingReportService
{
    private GetAccountAgingReportRepository $getAccountAgingReportRepository;

    public function __construct(PDO $connection)
    {
        $this->getAccountAgingReportRepository = new GetAccountAgingReportRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getReport(array $data): array
    {
        $sanitizedData = SanitizeReportDataService::sanitize($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $reportData = SetReportDataService::set($sanitizedData);
        $reportData->account_no = $data['accountNo'];
        $reports = $this->getAccountAgingReportRepository->getReport(
            $reportData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        $records = [];
        foreach ($reports as $report) {
            $records[] = [
                'customerName' => Utilities::decodeUTF8($report['customer_name']),
                'current' => floatval($report['0_days']),
                'days30' => floatval($report['30_days']),
                'days60' => floatval($report['60_days']),
                'days90' => floatval($report['90_days']),
                'days120' => floatval($report['120_days']),
                'days150' => floatval($report['150_days']),
                'days180' => floatval($report['180_days']),
                'totalBalance' => floatval($report['total_balance']),
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getAccountAgingReportRepository->getReport($reportData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return ['records' => $records, 'pagination' => $pagination];
        }

        return ['records' => $records];
    }
}
