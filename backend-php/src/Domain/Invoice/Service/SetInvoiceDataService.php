<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Data\InvoiceData;

final class SetInvoiceDataService
{
    public static function set(array $data): InvoiceData
    {
        $invoiceData = new InvoiceData();
        $invoiceData->invoice_type = GetInvoiceType::getType($data['invoiceType']);
        $invoiceData->sale_task_id = $data['saleTaskId'];
        $invoiceData->sale_id = $data['saleId'];
        $invoiceData->customer_id = $data['customerId'];
        $invoiceData->contact_id = $data['contactId'];
        $invoiceData->start_date = $data['startDate'];
        $invoiceData->end_date = $data['endDate'];
        $invoiceData->search = $data['search'];

        return $invoiceData;
    }
}
