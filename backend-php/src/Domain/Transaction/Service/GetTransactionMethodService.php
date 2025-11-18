<?php

namespace App\Domain\Transaction\Service;

use App\Domain\Transaction\Data\TransactionMethod;

final class GetTransactionMethodService
{
    public static function getMethod(?string $method): TransactionMethod
    {
        return match ($method) {
            'cash' => TransactionMethod::CASH,
            'credit_card' => TransactionMethod::CREDIT_CARD,
            'eft', 'bank_transfer' => TransactionMethod::BANK_TRANSFER,
            'mobile_payment' => TransactionMethod::MOBILE_PAYMENT,
            'other' => TransactionMethod::OTHER,
            default => TransactionMethod::NONE,
        };
    }
}
