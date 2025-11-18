<?php

namespace App\Domain\Transaction\Data;

enum TransactionMethod: string
{
    case CASH = 'cash';
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';
    case MOBILE_PAYMENT = 'mobile_payment';
    case OTHER = 'other';
    case NONE = '';
}
