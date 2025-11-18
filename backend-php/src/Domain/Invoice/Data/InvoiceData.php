<?php

namespace App\Domain\Invoice\Data;

final class InvoiceData
{
    public string $account_no;
    public string $sale_id;
    public string $sale_task_id;
    public int $invoice_no;
    public InvoiceType $invoice_type;
    public string $customer_id;
    public string $contact_id;
    public string $start_date;
    public string $end_date;
    public string $search;
}
