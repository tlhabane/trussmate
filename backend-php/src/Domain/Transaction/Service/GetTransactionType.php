<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Data\TransactionType;

final class GetTransactionType
{
    public static function getType(string $transaction_type): TransactionType
    {
        return match (strtolower(trim($transaction_type))) {
            'credit_memo', 'credit' => TransactionType::CREDIT_MEMO,
            'debit_memo', 'debit' => TransactionType::DEBIT_MEMO,
            'payment', 'cash' => TransactionType::PAYMENT,
            default => TransactionType::NONE
        };
    }
}
