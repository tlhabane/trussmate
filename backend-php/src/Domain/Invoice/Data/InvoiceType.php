<?php

namespace App\Domain\Invoice\Data;

enum InvoiceType: string
{
    case INVOICE = 'invoice';
    case PROFORMA = 'proforma_invoice';
    case NONE = '';
}
