<?php

namespace App\Domain\Transaction\Service;

use App\Domain\PDFTemplate\Service\Default\GetPDFClientDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFBankDetailService;
use App\Domain\PDFTemplate\Service\Default\GetPDFHeaderService;
use App\Domain\Account\Service\GetAccountInfoService;
use App\Domain\Transaction\Repository\GetTransactionBySaleIdRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Util\Files\CreatePDFFileService;
use App\Exception\ValidationException;
use PDO;

final class CreateTransactionDocumentService
{
    private GetPDFClientDetailService $getPDFClientDetailService;
    private GetPDFBankDetailService $getPDFBankDetailService;
    private GetPDFHeaderService $getPDFHeaderService;
    private GetAccountInfoService $getAccountInfoService;
    private GetTransactionByIdService $getTransactionByIdService;
    private GetTransactionBySaleIdRepository $getTransactionBySaleIdRepository;

    public function __construct(PDO $connection)
    {
        $this->getPDFClientDetailService = new GetPDFClientDetailService($connection);
        $this->getPDFBankDetailService = new GetPDFBankDetailService($connection);
        $this->getPDFHeaderService = new GetPDFHeaderService($connection);
        $this->getAccountInfoService = new GetAccountInfoService($connection);
        $this->getTransactionByIdService = new GetTransactionByIdService($connection);
        $this->getTransactionBySaleIdRepository = new GetTransactionBySaleIdRepository($connection);
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
    public function createDocument(array $data, string $directory = 'downloads'): array
    {
        $transaction = $this->getTransactionByIdService->getTransaction($data);
        if (empty($transaction)) {
            throw new ValidationException('Invalid or mission Transaction detail');
        }
        /* Calculate current balance */
        $payment_total = 0;
        $transactions = $this->getTransactionBySaleIdRepository->getTransaction($transaction['saleId']);
        foreach ($transactions->fetchAll(PDO::FETCH_ASSOC) as $trans) {
            $payment_total += $trans['transaction_amount'];
        }
        $current_balance = $transaction['jobTotal'] + $payment_total;
        /* Get account info */
        $accounts = $this->getAccountInfoService->getAccount($data['accountNo']);
        /* Document header & footer*/
        $documentProperties = [
            'accounts' => $accounts['records'],
            'documentTitle' => 'Transaction Detail',
            'salesRef' => $transaction['invoiceNo'],
            'salesDate' => $transaction['transactionDate']
        ];

        $headerAndFooter = $this->getPDFHeaderService->getHeaderAndFooter($documentProperties);
        $documentHeader = $headerAndFooter['header'];
        $documentFooter = $headerAndFooter['footer'];
        /* Customer details */
        $pdf_document = $this->getPDFClientDetailService->getClientDetail(
            $data['accountNo'],
            $transaction['customerId'],
            $transaction['contactId'] ?? ''
        );

        /* Summary */
        $pdf_document .= "<table style='width:100%; margin: 15px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
        $pdf_document .= "<thead>";
        $pdf_document .= "<tr>";
        $pdf_document .= "<th colspan='2' style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; text-align: left;'>Summary</th>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</thead>";
        $pdf_document .= "<tbody>";
        $pdf_document .= "<tr>";
        $pdf_document .= "<td style='width: 35%; padding: 8px 5px; vertical-align: middle;' class='inner contents'>Sale Ref.</td>";
        $pdf_document .= "<td style=' padding: 8px 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
        $pdf_document .= $transaction['saleNo'];
        $pdf_document .= "</td>";
        $pdf_document .= "</tr>";
        $pdf_document .= "<tr>";
        $pdf_document .= "<td style='width: 35%; padding: 5px; background-color: #f9f9f9; vertical-align: middle;' class='inner contents'>Sale Date</td>";
        $pdf_document .= "<td style='padding: 5px; background-color: #f9f9f9; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
        $pdf_document .= date('Y/m/d', strtotime($transaction['saleDate']));
        $pdf_document .= "</td>";
        $pdf_document .= "</tr>";
        $pdf_document .= "<tr>";
        $pdf_document .= "<td style='width: 35%; padding: 8px 5px; vertical-align: middle;' class='inner contents'>Sale Total</td>";
        $pdf_document .= "<td style=' padding: 8px 5px; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
        $pdf_document .= $this->formatAmount($transaction['jobTotal']);
        $pdf_document .= "</td>";
        $pdf_document .= "</tr>";
        $pdf_document .= "<tr>";
        $pdf_document .= "<td style='width: 35%; padding: 5px; background-color: #f9f9f9; vertical-align: middle;' class='inner contents'>Current Balance</td>";
        $pdf_document .= "<td style='padding: 5px; background-color: #f9f9f9; font-weight: bold; text-align: right; vertical-align: middle;' class='inner contents'>";
        $pdf_document .= $this->formatAmount($current_balance);
        $pdf_document .= "</td>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</tbody>";
        $pdf_document .= "</table>";

        /* Transaction items */
        $pdf_document .= "<table style='width:100%; margin: 25px 0; border-spacing: 3px; border-collapse: separate; font-size: 11px; color: #555555;'>";
        $pdf_document .= "<thead>";
        $pdf_document .= '<tr>';
        $pdf_document .= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; width: 15%; text-align: left;'>Date</th>";
        $pdf_document .= "<th colspan='2' style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; text-align: left;'>Description</th>";
        $pdf_document .= "<th style='border-bottom: 0.1mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; width: 15%; text-align: right;'>Amount[ZAR]</th>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</thead>";
        $pdf_document .= "<tbody>";
        $pdf_document .= "<tr>";
        $pdf_document .= "<td style='padding: 5px; vertical-align: middle;' class='inner contents'>";
        $pdf_document .= date('Y/m/d', strtotime($transaction['transactionDate']));
        $pdf_document .= "</td>";
        $pdf_document .= "<td colspan='2' style='padding: 5px; vertical-align: middle;' class='inner contents'>";
        $pdf_document .= "{$transaction['transactionDesc']}";
        $pdf_document .= "</td>";
        $pdf_document .= "<td style='padding: 5px; text-align: right; vertical-align: middle;' class='inner contents'>";
        $pdf_document .= $this->formatAmount($transaction['transactionAmount']);
        $pdf_document .= "</td>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</tbody>";
        $pdf_document .= "<tfoot>";
        $pdf_document .= '<tr>';
        $pdf_document .= "<th colspan='2' style='border-top: 0.1mm solid #efefef; padding: 10px 0; text-align: left;'>&nbsp;</th>";
        $pdf_document .= "<th style='border-bottom: 0.5mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; text-align: left;'>Total</th>";
        $pdf_document .= "<th style='border-bottom: 0.5mm solid #efefef; border-top: 0.1mm solid #efefef; padding: 10px 5px; width: 15%; text-align: right;'>";
        $pdf_document .= $this->formatAmount($transaction['transactionAmount']);
        $pdf_document .= "</th>";
        $pdf_document .= "</tr>";
        $pdf_document .= "</tfoot>";
        $pdf_document .= "</table>";

        /* Banking details */
        $pdf_document .= $this->getPDFBankDetailService->getBankAccount($data['accountNo']);

        /* Create PDF file */
        $pdfFileProperties = [
            'fileDir' => $directory,
            'fileName' => "Receipt-{$transaction['invoiceNo']}",
            'fileContent' => $pdf_document,
            'footerContent' => $documentFooter,
            'headerContent' => $documentHeader
        ];

        return CreatePDFFileService::createFile($pdfFileProperties);
    }
}
