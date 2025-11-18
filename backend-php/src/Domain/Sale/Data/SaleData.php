<?php

namespace App\Domain\Sale\Data;

final class SaleData
{
    public string $account_no;
    public string $sale_id;
    public int $sale_no;
    public SaleStatus $sale_status;
    public string $customer_id;
    public string $contact_id;
    public string $billing_address_id;
    public string $delivery_address_id;
    public string $workflow_id;
    public int $delivery_required;
    public int $labour_required;

    public string $search;
    public string $start_date;
    public string $end_date;
}
