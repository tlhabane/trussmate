<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Repository\GetInvoiceRepository;
use App\Exception\ValidationException;
use App\Util\DataPagination;
use App\Util\Utilities;
use PDO;

final class GetInvoiceService
{
    private GetInvoiceRepository $getInvoiceRepository;

    public function __construct(PDO $connection)
    {
        $this->getInvoiceRepository = new GetInvoiceRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getInvoice(array $data): array
    {
        $sanitizedData = SanitizeInvoiceDataService::sanitizedData($data);
        $paginationConfig = DataPagination::getRecordOffset(
            $sanitizedData['page'],
            $sanitizedData['recordsPerPage']
        );

        $invoiceData = SetInvoiceDataService::set($sanitizedData);
        $invoiceData->account_no = $data['accountNo'];

        $invoices = $this->getInvoiceRepository->getInvoice(
            $invoiceData,
            $paginationConfig['recordStart'],
            $paginationConfig['recordsPerPage']
        );

        $records = [];
        foreach ($invoices as $invoice) {
            $invoice_balance = $invoice['invoice_amount'] + $invoice['payment_total'];
            $records[] = [
                'invoiceNo' => Utilities::addPadding($invoice['invoice_no'], 0),
                'invoiceType' => $invoice['invoice_type'],
                'invoiceAmount' => floatval($invoice['invoice_amount']),
                'paymentTotal' => floatval($invoice['payment_total']),
                'invoiceBalance' => floatval($invoice_balance),
                'saleTaskId' => $invoice['sale_task_id'],
                'saleId' => $invoice['sale_id'],
                'saleNo' => Utilities::addPadding($invoice['sale_no'], 0),
                'customerId' => $invoice['customer_id'],
                'customerName' => Utilities::decodeUTF8($invoice['customer_name']),
                'customerEmail' => $invoice['customer_email'],
                'contactId' => $invoice['contact_id'],
                'firstName' => Utilities::decodeUTF8($invoice['first_name']),
                'lastName' => Utilities::decodeUTF8($invoice['last_name']),
                'contactEmail' => $invoice['contact_email'],
                'invoiceDate' => date('c', strtotime($invoice['created'])),
                'invoiceDueDays' => intval($invoice['task_days']),
                'invoiceDueDate' => date('c', strtotime($invoice['invoice_due_date']))
            ];
        }

        if ($sanitizedData['page'] > 0) {
            $countRecords = $this->getInvoiceRepository->getInvoice($invoiceData);
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
