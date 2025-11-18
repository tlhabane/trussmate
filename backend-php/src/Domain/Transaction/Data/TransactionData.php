<?php

namespace App\Domain\Transaction\Data;

final class TransactionData
{
    public string $account_no;
    public string $user_id;
    public int $invoice_no;
    public string $transaction_id;
    public float $transaction_amount;
    public TransactionType $transaction_type;
    public TransactionMethod $transaction_method;
    public string $transaction_date;
    public string $transaction_desc;
    public string $sale_id;
    public string $customer_id;
    public string $contact_id;
    public string $search;
    public string $start_date;
    public string $end_date;
}
