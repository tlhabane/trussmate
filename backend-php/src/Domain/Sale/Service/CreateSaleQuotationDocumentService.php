<?php

namespace App\Domain\Sale\Service;

use App\Domain\Task\Data\TaskAction;
use App\Domain\PDFTemplate\Service\Default\GetPDFClientDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFSalesTermsService;
use App\Domain\PDFTemplate\Service\Default\GetPDFSalesItemsService;
use App\Domain\PDFTemplate\Service\Default\GetPDFHeaderService;
use App\Domain\Account\Service\GetAccountInfoService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Util\Files\CreatePDFFileService;
use App\Exception\ValidationException;
use Exception;
use PDO;

final class CreateSaleQuotationDocumentService
{
    private GetPDFClientDetailService $getPDFClientDetailService;
    private GetPDFSalesTermsService $getPDFSalesTermsService;
    private GetPDFSalesItemsService $getPDFSalesItemsService;
    private GetPDFHeaderService $getPDFHeaderService;
    private GetAccountInfoService $getAccountInfoService;

    public function __construct(PDO $connection)
    {
        $this->getPDFSalesTermsService = new GetPDFSalesTermsService($connection);
        $this->getPDFSalesItemsService = new GetPDFSalesItemsService();
        $this->getPDFClientDetailService = new GetPDFClientDetailService($connection);
        $this->getPDFHeaderService = new GetPDFHeaderService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws Exception
     */
    public function getDocument(array $data, mixed $sale, string $directory = 'downloads'): array
    {
        $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);
        /* Document header & footer*/
        $documentProperties = [
            'accounts' => $accounts['records'],
            'documentTitle' => 'Quotation',
            'salesRef' => $sale['saleNo'],
            'salesDate' => $sale['saleDate']
        ];

        $headerAndFooter = $this->getPDFHeaderService->getHeaderAndFooter($documentProperties);
        $documentHeader = $headerAndFooter['header'];
        $documentFooter = $headerAndFooter['footer'];
        /* Customer details */
        $pdf_document = $this->getPDFClientDetailService->getClientDetail(
            $data['accountNo'],
            $sale['customer']['customerId'],
            $sale['contact']['contactId'] ?? ''
        );

        /* Sale(line) items */
        $pdf_document .= $this->getPDFSalesItemsService->getSaleItems($sale['jobs']);
        /* Quotation terms */
        $pdf_document .= $this->getPDFSalesTermsService->getSalesTerms($data);
        /* Payment schedule */
        $paymentTasks = array_filter(
            $sale['tasks'],
            fn($otherTask) => (
                $otherTask['taskPayment'] > 0 &&
                $otherTask['taskAction'] !== TaskAction::PENALTY->value &&
                $otherTask['saleTaskId'] !== $data['saleTaskId']
            )
        );

        if (count($paymentTasks) > 0) {
            $pdf_document .= "<table style='width:100%; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555; page-break-inside: avoid;'>";
            $pdf_document .= "<tr>";
            $pdf_document .= "<td style='border-top: 0.1mm solid #efefef; border-bottom: 0.1mm solid #efefef; font-weight: bold; padding: 10px 5px;'>";
            $pdf_document .= 'Payment Schedule';
            $pdf_document .= "</td>";
            $pdf_document .= "<td style='text-align: right; border-top: 0.1mm solid #efefef; border-bottom: 0.1mm solid #efefef; font-weight: bold; padding: 10px 5px;'>";
            $pdf_document .= 'Amount';
            $pdf_document .= "</td>";
            $pdf_document .= "</tr>";

            foreach ($paymentTasks as $paymentTask) {
                $total_payable = $paymentTask['taskPayment'];
                if ($paymentTask['taskPaymentType'] === 'percentage') {
                    $total_payable = ($paymentTask['taskPayment'] / 100) * $sale['saleTotal'];
                }

                $pdf_document .= "<tr>";
                $pdf_document .= "<td style='padding: 3px 5px;'>";
                $pdf_document .= $paymentTask['taskName'];
                $pdf_document .= "</td>";
                $pdf_document .= "<td style='padding: 3px 5px; text-align: right;'>";
                $pdf_document .= number_format($total_payable, 2);
                $pdf_document .= "</td>";
                $pdf_document .= "</tr>";
            }
            $pdf_document .= "</table>";
        }
        $pdfFileProperties = [
            'fileDir' => $directory,
            'fileName' => "Quotation-{$sale['saleNo']}",
            'fileContent' => $pdf_document,
            'footerContent' => $documentFooter,
            'headerContent' => $documentHeader
        ];

        return CreatePDFFileService::createFile($pdfFileProperties);
    }
}
