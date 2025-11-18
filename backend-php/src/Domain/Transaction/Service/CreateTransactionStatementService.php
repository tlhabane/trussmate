<?php

namespace App\Domain\Transaction\Service;

use App\Domain\PDFTemplate\Service\Default\GetPDFClientDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFBankDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFHeaderService;
use App\Domain\Report\Service\GetAccountAgingReportService;
use App\Domain\Account\Service\GetAccountInfoService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Util\Files\CreatePDFFileService;
use App\Exception\ValidationException;
use PDO;

final class CreateTransactionStatementService
{
    private GetTransactionService $getTransactionService;
    private GetPDFClientDetailService $getPDFClientDetailService;
    private GetPDFBankDetailService $getPDFBankDetailService;
    private GetPDFHeaderService $getPDFHeaderService;
    private GetAccountAgingReportService $getAccountAgingReportService;
    private GetAccountInfoService $getAccountInfoService;

    public function __construct(PDO $connection)
    {
        $this->getTransactionService = new GetTransactionService($connection);
        $this->getPDFClientDetailService = new GetPDFClientDetailService($connection);
        $this->getPDFBankDetailService = new GetPDFBankDetailService($connection);
        $this->getPDFHeaderService = new GetPDFHeaderService($connection);
        $this->getAccountAgingReportService = new GetAccountAgingReportService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
    }

    private function formatAmount(float $amount): string
    {
        $formattedAmount = number_format(abs($amount), 2);
        return $amount < 0 ? "({$formattedAmount})" : $formattedAmount;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     */
    public function createStatement(array $data, string $directory = 'downloads'): array
    {
        $transactions = $this->getTransactionService->getTransaction($data);
        $sanitizedData = SanitizeTransactionDataService::sanitizeData($data);
        $opening_balance = 0;
        if (!empty($sanitizedData['startDate'])) {
            $endDate = date_create($sanitizedData['startDate'])->modify('-1 day')->format('Y-m-d');
            $startDate = "2025-01-01";
            $transactionData = array_merge($sanitizedData, ['startDate' => $startDate, 'endDate' => $endDate]);
            $openingBalanceData = $this->getTransactionService->getTransaction($transactionData);
            foreach ($openingBalanceData['records'] as $trans) {
                $opening_balance += $trans['transactionAmount'];
            }
        }

        /* Get account info */
        $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);
        /* Document header & footer*/
        $documentProperties = [
            'accounts' => $accounts['records'],
            'documentTitle' => 'Transaction Statement',
            'salesDate' => date('c', time())
        ];

        $headerAndFooter = $this->getPDFHeaderService->getHeaderAndFooter($documentProperties);
        $documentHeader = $headerAndFooter['header'];
        $documentFooter = $headerAndFooter['footer'];
        /* Customer details */
        $pdf_document = '';
        if (!empty($sanitizedData['customerId'])) {
            $pdf_document .= $this->getPDFClientDetailService->getClientDetail(
                $data['accountNo'],
                $sanitizedData['customerId'],
                $sanitizedData['contactId'] ?? ''
            );
        }
        /* Transaction items */
        $pdf_document .= "<table style='width:100%; margin: 25px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
        $pdf_document .= "<thead>";
        $pdf_document .= '<tr>';
        $pdf_document .= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; width: 20%; text-align: left;'>Date</th>";
        $pdf_document .= "<th colspan='2' style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; text-align: left;'>Description</th>";
        $pdf_document .= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; width: 20%; text-align: right;'>Amount[ZAR]</th>";
        $pdf_document .= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; width: 20%; text-align: right;'>Balance[ZAR]</th>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</thead>";
        $pdf_document .= "<tbody>";
        $pdf_document .= "<tr style='background-color: #f9f9f9;'>";
        $pdf_document .= "<td style='padding: 8px 5px; text-align: left;'>&nbsp;</td>";
        $pdf_document .= "<td colspan='2' style='padding: 8px 5px'>";
        $pdf_document .= "Opening Balance";
        $pdf_document .= "</td>";
        $pdf_document .= "<td colspan='2' style='padding: 8px 5px; text-align: right;'>";
        $pdf_document .= $this->formatAmount($opening_balance);
        $pdf_document .= "</td>";
        $pdf_document .= '</tr>';
        $last_date = '';
        foreach (array_reverse($transactions['records']) as $index => $transaction) {
            $opening_balance += $transaction['transactionAmount'];
            $last_date = date('Y/m/d', strtotime($transaction['transactionDate']));
            $pdf_document .= ($index % 2) ? "<tr style='background-color: #f9f9f9;'>" : "<tr>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: left;'>";
            $pdf_document .= date('Y/m/d', strtotime($transaction['transactionDate']));
            $pdf_document .= "</td>";
            $pdf_document .= "<td colspan='2' style='padding: 8px 5px; text-align: left;'>";
            if (empty($transaction['transactionDesc'])) {
                $pdf_document .= ucwords(implode(' ', explode('_', $transaction['transactionType'])));
                if (!empty($transaction['invoiceNo'])) {
                    $pdf_document .= " #{$transaction['invoiceNo']}";
                }
            } else {
                $pdf_document .= $transaction['transactionDesc'];
            }
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= $this->formatAmount($transaction['transactionAmount']);
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= $this->formatAmount($opening_balance);
            $pdf_document .= "</td>";
            $pdf_document .= "</tr>";
        }
        $pdf_document .= "</tbody>";
        $pdf_document .= "<tfoot>";
        $pdf_document .= '<tr>';
        $pdf_document .= "<th style='border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: left;'>&nbsp;</th>";
        $pdf_document .= "<th colspan='2' style='border-bottom: 0.5mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; text-align: left;'>";
        $pdf_document .= "Balance";
        $pdf_document .= empty($last_date) ? '' : " as at {$last_date}";
        $pdf_document .= "</th>";
        $pdf_document .= "<th colspan='2' style='border-bottom: 0.5mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; text-align: right;'>";
        $pdf_document .= $this->formatAmount($opening_balance);
        $pdf_document .= "</th>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</tfoot>";
        $pdf_document .= "</table>";

        $agingReports = $this->getAccountAgingReportService->getReport($data);
        foreach ($agingReports['records'] as $report) {
            $ninetyDays = $report['days180'] + $report['days150'] + $report['days120'] + $report['days90'];
            $style = "border: 0.1mm solid #efefef; padding: 10px 5px; text-align: right; width: 20%";
            $pdf_document .= "<table style='width:100%; margin: 25px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
            $pdf_document .= '<tr>';
            $pdf_document .= '<td style="' . $style . '">90+ days</td>';
            $pdf_document .= '<td style="' . $style . '">60 days</td>';
            $pdf_document .= '<td style="' . $style . '">30 days</td>';
            $pdf_document .= '<td style="' . $style . '">Current</td>';
            $pdf_document .= '<td style="' . $style . '">Due</td>';
            $pdf_document .= '</tr>';
            $pdf_document .= '<tr>';
            $pdf_document .= '<th style="' . $style . '">';
            $pdf_document .= $this->formatAmount($ninetyDays);
            $pdf_document .= '</th>';
            $pdf_document .= '<th style="' . $style . '">';
            $pdf_document .= $this->formatAmount($report['days60']);
            $pdf_document .= '</th>';
            $pdf_document .= '<th style="' . $style . '">';
            $pdf_document .= $this->formatAmount($report['days30']);
            $pdf_document .= '</th>';
            $pdf_document .= '<th style="' . $style . '">';
            $pdf_document .= $this->formatAmount($report['current']);
            $pdf_document .= '</th>';
            $pdf_document .= '<th style="' . $style . '">';
            $pdf_document .= $this->formatAmount($report['totalBalance']);
            $pdf_document .= '</th>';
            $pdf_document .= '</tr>';
            $pdf_document .= "</table>";
        }

        /* Banking details */
        $pdf_document .= $this->getPDFBankDetailService->getBankAccount($data['accountNo']);

        /* Create PDF file */
        $pdfFileProperties = [
            'fileDir' => $directory,
            'fileName' => "Statement-" . date('YmdHis'),
            'fileContent' => $pdf_document,
            'footerContent' => $documentFooter,
            'headerContent' => $documentHeader
        ];

        return CreatePDFFileService::createFile($pdfFileProperties);
    }
}
