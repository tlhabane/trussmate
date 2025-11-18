<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Repository\GetTransactionRepository;
use App\Domain\Invoice\Service\GetInvoiceByTaskIdService;
use App\Exception\ValidationException;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetTransactionService
{
    private GetInvoiceByTaskIdService $getInvoiceByTaskIdService;
    private GetTransactionRepository $getTransactionRepository;

    public function __construct(PDO $connection)
    {
        $this->getInvoiceByTaskIdService = new GetInvoiceByTaskIdService($connection);
        $this->getTransactionRepository = new GetTransactionRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getTransaction(array $data): array
    {
        $sanitizedData = SanitizeTransactionDataService::sanitizeData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $transactionData = SetTransactionDataService::set($sanitizedData);
        $transactionData->account_no = $data['accountNo'] ?? '';

        $transactions = $this->getTransactionRepository->getTransaction(
            $transactionData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        $records = [];
        foreach ($transactions as $transaction) {
            $invoice_balance = 0;
            $sale_task_id = '';
            if ($transaction['transaction_type'] !== 'payment') {
                $invoice_data = [
                    'accountNo' => $data['accountNo'] ?? '',
                    'sessionUsername' => $data['sessionUsername'],
                    'invoiceNo' => $transaction['invoice_no'],
                ];
                $invoices = $this->getInvoiceByTaskIdService->getInvoice($invoice_data);
                foreach ($invoices['records'] as $invoice) {
                    $sale_task_id = $invoice['saleTaskId'];
                    $invoice_balance += $invoice['invoiceBalance'];
                }
            }

            $records[] = [
                'saleId' => $transaction['sale_id'],
                'saleNo' => Utilities::addPadding($transaction['sale_no'], 0),
                'invoiceNo' => Utilities::addPadding($transaction['invoice_no'], 0),
                'saleTaskId' => $sale_task_id,
                'invoiceBalance' => floatval($invoice_balance),
                'transactionId' => $transaction['transaction_id'],
                'transactionCancelled' => intval($transaction['transaction_cancelled']),
                'transactionType' => $transaction['transaction_type'],
                'transactionDate' => date('c', strtotime($transaction['transaction_date'])),
                'postingDate' => date('c', strtotime($transaction['posting_date'])),
                'transactionAmount' => floatval($transaction['transaction_amount']),
                'transactionDesc' => Utilities::decodeUTF8($transaction['transaction_desc']),
                'transactionMethod' => $transaction['transaction_method'],
                'customerId' => $transaction['customer_id'],
                'customerName' => Utilities::decodeUTF8($transaction['customer_name']),
                'customerEmail' => $transaction['customer_email'],
                'contactId' => $transaction['contact_id'],
                'firstName' => Utilities::decodeUTF8($transaction['first_name']),
                'lastName' => Utilities::decodeUTF8($transaction['last_name']),
                'contactEmail' => $transaction['contact_email'],
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getTransactionRepository->getTransaction($transactionData);
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
