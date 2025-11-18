<?php

namespace App\Domain\SaleDocument\Service;

use App\Domain\SaleDocument\Data\SaleDocumentData;

final class SetSaleDocumentDataService
{
    public static function set(array $data): SaleDocumentData
    {
        $document = new SaleDocumentData();
        $document->sale_id = $data['saleId'];
        $document->sale_task_id = $data['saleTaskId'];
        $document->doc_id = $data['docId'];
        $document->doc_type = GetDocumentTypeService::getType($data['docType']);
        $document->doc_src = $data['docSrc'];
        $document->doc_name = $data['docName'];

        return $document;
    }
}
