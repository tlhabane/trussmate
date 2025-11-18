<?php

namespace App\Domain\SaleJob\Data;

class SetSaleJobQueryData
{
    public static function set(SaleJobData $data): array
    {
        return [
            'sale_id' => $data->sale_id,
            'job_no' => $data->job_no,
            'job_description' => $data->job_description,
            'design_info' => $data->design_info,
            'line_items' => $data->line_items,
            'subtotal' => $data->subtotal,
            'vat' => $data->vat,
            'total' => $data->total
        ];
    }
}
