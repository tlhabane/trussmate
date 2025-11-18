<?php

namespace App\Domain\Sale\Service;

use App\Domain\Sale\Data\SaleData;

final class SetSaleDataService
{
    public static function set(array $data): SaleData
    {
        $saleData = new SaleData();
        $saleData->sale_id = $data['saleId'];
        $saleData->sale_no = $data['saleNo'];
        $saleData->sale_status = GetSaleStatusService::getStatus($data['saleStatus']);
        $saleData->customer_id = $data['customerId'];
        $saleData->contact_id = $data['contactId'];
        $saleData->billing_address_id = $data['billingAddressId'];
        $saleData->delivery_address_id = $data['deliveryAddressId'];
        $saleData->workflow_id = $data['workflowId'];
        $saleData->delivery_required = $data['deliveryRequired'];
        $saleData->labour_required = $data['labourRequired'];
        $saleData->start_date = $data['startDate'];
        $saleData->end_date = $data['endDate'];
        $saleData->search = $data['search'];

        return $saleData;
    }
}
