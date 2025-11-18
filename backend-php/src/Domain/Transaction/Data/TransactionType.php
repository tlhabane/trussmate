<?php

namespace App\Domain\Transaction\Data;

enum TransactionType: string
{
    case CREDIT_MEMO = 'credit_memo';
    case DEBIT_MEMO = 'debit_memo';
    case PAYMENT = 'payment';
    case NONE = '';
}
