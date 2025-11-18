<?php

namespace App\Domain\SaleDocument\Data;

enum DocumentType: string
{
    case FLOOR_PLAN = 'floor_plan';
    case ESTIMATE = 'estimate';
    case PROFORMA = 'proforma';
    case INVOICE = 'invoice';
    case QUOTATION = 'quotation';
    case OTHER = 'other';
    case NONE = '';
}
