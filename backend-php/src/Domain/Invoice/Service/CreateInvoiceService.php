<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Account\Service\GetAccountInfoService;
use App\Domain\PDFTemplate\Service\Default\GetPDFBankDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFClientDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFHeaderService;
use App\Domain\PDFTemplate\Service\Default\GetPDFSalesTermsService;
use App\Domain\PDFTemplate\Service\Default\GetPDFSummarySalesItemsService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Domain\Sale\Service\GetSaleService;
use App\Exception\ValidationException;
use App\Util\Files\CreatePDFFileService;
use App\Util\Utilities;
use DateInterval;
use Exception;
use PDO;

final class CreateInvoiceService
{
    private GetPDFClientDetailService $getPDFClientDetailService;
    private GetPDFSalesTermsService $getPDFSalesTermsService;
    private GetPDFSummarySalesItemsService $getPDFSummarySalesItemsService;
    private GetPDFBankDetailService $getPDFBankDetailService;
    private GetPDFHeaderService $getPDFHeaderService;
    private GetAccountInfoService $getAccountInfoService;
    private AddInvoiceService $addInvoiceService;
    private GetSaleService $getSaleService;

    public function __construct(PDO $connection)
    {
        $this->getPDFSalesTermsService = new GetPDFSalesTermsService($connection);
        $this->getPDFBankDetailService = new GetPDFBankDetailService($connection);
        $this->getPDFSummarySalesItemsService = new GetPDFSummarySalesItemsService();
        $this->getPDFClientDetailService = new GetPDFClientDetailService($connection);
        $this->getPDFHeaderService = new GetPDFHeaderService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
        $this->addInvoiceService = new AddInvoiceService($connection);
        $this->getSaleService = new GetSaleService($connection);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws Exception
     */
    public function getInvoice(array $data, string $directory = 'downloads'): array
    {
        $sales = $this->getSaleService->getSale($data);
        foreach ($sales['records'] as $sale) {
            $documentTitle = 'Invoice';
            $invoiceType = 'invoice';
            $currentTasks = array_filter(
                $sale['tasks'],
                fn($otherTask) => $otherTask['saleTaskId'] === $data['saleTaskId']
            );
            foreach ($currentTasks as $task) {
                $invoiceType = $task['taskAction'];
                $documentTitle = ucwords(implode(' ', explode('_', $task['taskAction'])));
            }

            $invoice = $this->addInvoiceService->addInvoice(array_merge($data, [
                'invoiceType' => $invoiceType
            ]));
            $invoiceNo = Utilities::addPadding($invoice['id'], 0);

            $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);
            /* Document header & footer*/
            $documentProperties = [
                'accounts' => $accounts['records'],
                'documentTitle' => $documentTitle,
                'salesRef' => $invoiceNo,
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
            $pdf_document .= $this->getPDFSummarySalesItemsService->getSaleItems($sale['jobs']);
            $amountDue = 0;
            $amountDueDate = '';
            foreach ($currentTasks as $term) {
                if ($term['taskPayment'] > 0 && $sale['saleTotal'] > 0) {
                    $dateTimeObject = date_create();
                    $interval = DateInterval::createFromDateString("{$term['taskDays']} day");
                    $dateTimeObject->add($interval);
                    $depositAmount = $sale['saleTotal'] * ($term['taskPayment'] / 100);
                    $amountDue = $depositAmount;
                    $amountDueDate = $dateTimeObject->format('Y/m/d');

                    $pdf_document .= "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
                    $pdf_document .= '<tr>';
                    $pdf_document .= '<td colspan="2" style="border-top: 0.1mm solid #efefef; border-bottom: 0.1mm solid #efefef; font-weight: bold; padding: 10px 5px;">';
                    $pdf_document .= "Due as of Today - {$term['taskName']}";
                    $pdf_document .= '</td>';
                    $pdf_document .= '</tr>';
                    $pdf_document .= '<tr>';
                    $pdf_document .= '<td style="width: 35%; padding: 5px; vertical-align: middle;;">Amount Due</td>';
                    $pdf_document .= '<td style="padding:5px; font-weight: bold; text-align: right; vertical-align: middle;">' . number_format($depositAmount, 2) . '</td>';
                    $pdf_document .= '</tr>';
                    $pdf_document .= '<tr>';
                    $pdf_document .= '<td style="background-color: #f9f9f9; padding: 5px; vertical-align: middle;">Payable By</td>';
                    $pdf_document .= '<td style="background-color: #f9f9f9; padding:5px; font-weight: bold; text-align: right; vertical-align: middle;">' . $dateTimeObject->format('Y/m/d') . '</td>';
                    $pdf_document .= '</tr>';
                    $pdf_document .= "</table>";
                }
            }
            /* Terms */
            $pdf_document .= $this->getPDFSalesTermsService->getSalesTerms($data);

            /* Banking details */
            $pdf_document .= $this->getPDFBankDetailService->getBankAccount($data['accountNo']);

            $filename_prefix = implode('-', explode(' ', $documentTitle));
            $pdfFileProperties = [
                'fileDir' => $directory,
                'fileName' => "{$filename_prefix}-{$invoiceNo}",
                'fileContent' => $pdf_document,
                'footerContent' => $documentFooter,
                'headerContent' => $documentHeader
            ];

            $document = CreatePDFFileService::createFile($pdfFileProperties);

            return array_merge($document, $invoice, [
                'invoiceNo' => $invoiceNo,
                'documentRef' => $documentTitle,
                'amountDue' => $amountDue,
                'amountDueDate' => $amountDueDate
            ]);
        }

        throw new ValidationException('Invalid or missing sale details');
    }
}
