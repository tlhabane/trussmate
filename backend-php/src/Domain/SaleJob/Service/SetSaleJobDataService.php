<?php

namespace App\Domain\SaleJob\Service;

use App\Domain\SaleJob\Data\SaleJobData;

final class SetSaleJobDataService
{
    public static function set(array $data): SaleJobData
    {
        $saleJobData = new SaleJobData();
        $saleJobData->sale_id = $data['saleId'];
        $saleJobData->job_no = $data['jobNo'];
        $saleJobData->job_description = $data['jobDescription'];
        $saleJobData->design_info = $data['designInfo'];
        $saleJobData->line_items = $data['lineItems'];
        $saleJobData->subtotal = $data['subtotal'];
        $saleJobData->vat = $data['vat'];
        $saleJobData->total = $data['total'];

        return $saleJobData;
    }
}
