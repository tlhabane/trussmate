<?php

namespace App\Domain\Report\Service;

use App\Domain\PDFTemplate\Service\Default\GetPDFBankDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFHeaderService;
use App\Domain\Account\Service\GetAccountInfoService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Util\Files\CreatePDFFileService;
use App\Exception\ValidationException;
use PDO;

final class CreateAccountAgingReportDocumentService
{
    private GetPDFBankDetailService $getPDFBankDetailService;
    private GetPDFHeaderService $getPDFHeaderService;
    private GetAccountInfoService $getAccountInfoService;
    private GetAccountAgingReportService $getAccountAgingReportService;

    public function __construct(PDO $connection)
    {
        $this->getPDFBankDetailService = new GetPDFBankDetailService($connection);
        $this->getPDFHeaderService = new GetPDFHeaderService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
        $this->getAccountAgingReportService = new GetAccountAgingReportService($connection);
    }

    /**
     * @throws ValidationException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function createDocument(array $data, $directory = 'downloads'): array
    {
        $reportData = $this->getAccountAgingReportService->getReport($data);
        /* Get account info */
        $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);
        /* Document header & footer*/
        $documentProperties = [
            'accounts' => $accounts['records'],
            'documentTitle' => 'Accounts Aging Report',
            'salesDate' => date('c', time())
        ];

        $headerAndFooter = $this->getPDFHeaderService->getHeaderAndFooter($documentProperties);
        $documentHeader = $headerAndFooter['header'];
        $documentFooter = $headerAndFooter['footer'];

        $borders = "border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef;";
        $pdf_document = "<table style='width:100%; margin: 25px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
        $pdf_document .= "<thead>";
        $pdf_document .= '<tr>';
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; width: 20%; text-align: left;'>Customer Name</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>180+ Days</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>150 Days</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>120 Days</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>90 Days</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>60 Days</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>30 Days</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>Current</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>Total</th>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</thead>";
        $pdf_document .= "<tbody>";
        foreach ($reportData['records'] as $index => $report) {
            $pdf_document .= ($index % 2) ? "<tr style='background-color: #f9f9f9;'>" : "<tr>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: left;'>";
            $pdf_document .= $report['customerName'];
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['days180'], 2, '.', ',');
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['days150'], 2, '.', ',');
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['days120'], 2, '.', ',');
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['days90'], 2, '.', ',');
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['days60'], 2, '.', ',');
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['days30'], 2, '.', ',');
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['current'], 2, '.', ',');
            $pdf_document .= "</td>";
            $pdf_document .= "<th style='padding: 8px 5px; text-align: right;'>";
            $pdf_document .= number_format($report['totalBalance'], 2, '.', ',');
            $pdf_document .= "</th>";
            $pdf_document .= '</tr>';
        }
        $pdf_document .= "</tbody>";
        $pdf_document .= "<tfoot>";
        $pdf_document .= '<tr>';
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; width: 20%; text-align: left;'>Total</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'days180')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'days150')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'days120')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'days90')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'days60')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'days30')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'current')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "<th style='{$borders} padding: 10px 5px; text-align: right;'>";
        $pdf_document .= number_format(array_sum(array_column($reportData['records'], 'totalBalance')), 2, '.', ',');
        $pdf_document .= "</th>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</tfoot>";
        $pdf_document .= "</table>";

        /* Banking details */
        $pdf_document .= $this->getPDFBankDetailService->getBankAccount($data['accountNo']);

        /* Create PDF file */
        $pdfFileProperties = [
            'fileDir' => $directory,
            'fileName' => "Account-Aging-Report-" . date('Ymd'),
            'fileContent' => $pdf_document,
            'footerContent' => $documentFooter,
            'headerContent' => $documentHeader
        ];

        return CreatePDFFileService::createFile($pdfFileProperties, 'L');
    }
}
