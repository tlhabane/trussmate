<?php

namespace App\Domain\SaleDocument\Service;

use App\Domain\SaleDocument\Data\DocumentType;

final class GetDocumentTypeService
{
    public static function getType(string $doc_type): DocumentType
    {
        return match (trim(strtolower($doc_type))) {
            'floor_plan' => DocumentType::FLOOR_PLAN,
            'estimate' => DocumentType::ESTIMATE,
            'proforma' => DocumentType::PROFORMA,
            'invoice' => DocumentType::INVOICE,
            'quotation' => DocumentType::QUOTATION,
            'other' => DocumentType::OTHER,
            default => DocumentType::NONE
        };
    }
}
