<?php

namespace App\Domain\Sale\Data;

class MapSaleQueryData
{
    public static function map(SaleData $data): array
    {
        return [
            'sale_id' => $data->sale_id,
            'customer_id' => $data->customer_id,
            'contact_id' => $data->contact_id,
            'billing_address_id' => $data->billing_address_id,
            'delivery_address_id' => $data->delivery_address_id,
            'workflow_id' => $data->workflow_id,
            'delivery_required' => $data->delivery_required,
            'labour_required' => $data->labour_required
        ];
    }
}
