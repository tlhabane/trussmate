<?php

namespace App\Domain\Report\Service;

use App\Domain\Report\Repository\GetAccountBalancesReportRepository;
use App\Exception\ValidationException;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetAccountBalancesReportService
{
    private GetAccountBalancesReportRepository $getAccountBalancesReportRepository;

    public function __construct(PDO $connection)
    {
        $this->getAccountBalancesReportRepository = new GetAccountBalancesReportRepository($connection);
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
        $reports = $this->getAccountBalancesReportRepository->getReport(
            $reportData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        $records = [];
        foreach ($reports as $report) {
            $records[] = [
                'invoiceMonth' => Utilities::decodeUTF8($report['invoice_month']),
                'saleTotal' => floatval($report['sale_total']),
                'paymentTotal' => floatval($report['payment_total']),
                'invoiceBalance' => floatval($report['invoice_balance']),
                'overdueInvoiceBalance' => floatval($report['overdue_invoice_balance']),
                'averageDaysOverdue' => floatval($report['average_days_overdue']),
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getAccountBalancesReportRepository->getReport($reportData);
            $pagination = DataPagination::getPagingLinks(
                $sanitizedData['page'],
                $countRecords->rowCount(),
                $paginationConfig['recordsPerPage']
            );

            return [
                'records' => $records,
                'pagination' => $pagination
            ];
        }

        return [
            'records' => $records
        ];
    }
}
