<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Data\TransactionData;

final class SetTransactionDataService
{
    public static function set(array $data): TransactionData
    {
        $transactionData = new TransactionData();
        $transactionData->invoice_no = $data['invoiceNo'];
        $transactionData->transaction_id = $data['transactionId'];
        $transactionData->transaction_type = GetTransactionType::getType($data['transactionType']);
        $transactionData->transaction_amount = $data['transactionAmount'];
        $transactionData->transaction_date = $data['transactionDate'];
        $transactionData->transaction_method = GetTransactionMethodService::getMethod($data['transactionMethod']);
        $transactionData->transaction_desc = $data['transactionDesc'];
        $transactionData->sale_id = $data['saleId'];
        $transactionData->customer_id = $data['customerId'];
        $transactionData->contact_id = $data['contactId'];
        $transactionData->start_date = $data['startDate'];
        $transactionData->end_date = $data['endDate'];
        $transactionData->search = $data['search'];

        return $transactionData;
    }
}
