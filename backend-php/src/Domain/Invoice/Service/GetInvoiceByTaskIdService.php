<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Repository\GetInvoiceByTaskIdRepository;
use App\Domain\Invoice\Repository\GetInvoiceByInvoiceNoRepository;
use App\Exception\ValidationException;
use App\Util\Utilities;
use PDO;

final class GetInvoiceByTaskIdService
{
    private GetInvoiceByTaskIdRepository $getInvoiceByTaskIdRepository;
    private GetInvoiceByInvoiceNoRepository $getInvoiceByInvoiceNoRepository;

    public function __construct(PDO $connection)
    {
        $this->getInvoiceByTaskIdRepository = new GetInvoiceByTaskIdRepository($connection);
        $this->getInvoiceByInvoiceNoRepository = new GetInvoiceByInvoiceNoRepository($connection);
    }

    /**
     * @throws ValidationException
     */
    public function getInvoice(array $data): array
    {
        $sanitizedData = SanitizeInvoiceDataService::sanitizedData($data);
        $invoice_no = intval(Utilities::removePadding($sanitizedData['invoiceNo']));
        if ($invoice_no > 0) {
            $invoices = $this->getInvoiceByInvoiceNoRepository->getInvoice($invoice_no);
        } else {
            $invoices = $this->getInvoiceByTaskIdRepository->getInvoice($sanitizedData['saleTaskId']);
        }

        $records = [];
        foreach ($invoices as $invoice) {
            $total_payable = $invoice['task_payment'];
            if ($invoice['task_payment_type'] == 'percentage') {
                $total_payable = ($invoice['task_payment'] / 100) * $invoice['job_total'];
            }
            $invoice_balance = $total_payable + $invoice['payment_total'] ?? 0;
            $records[] = [
                'invoiceNo' => Utilities::addPadding($invoice['invoice_no'], 0),
                'invoiceType' => $invoice['invoice_type'],
                'invoiceAmount' => floatval($total_payable),
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

        return ['records' => $records];
    }
}
