<?php

namespace App\Domain\Invoice\Service;

use App\Domain\Invoice\Data\InvoiceType;

final class GetInvoiceType
{
    public static function getType(string $invoice_type): InvoiceType
    {
        return match (strtolower(trim($invoice_type))) {
            'invoice' => InvoiceType::INVOICE,
            'proforma', 'proforma_invoice' => InvoiceType::PROFORMA,
            default => InvoiceType::NONE
        };
    }
}
