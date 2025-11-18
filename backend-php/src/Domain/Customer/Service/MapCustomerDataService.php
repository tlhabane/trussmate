<?php

namespace App\Domain\Customer\Service;

use App\Domain\Customer\Data\CustomerData;

final class MapCustomerDataService
{
    public static function map(array $data): CustomerData
    {
        $customer = new CustomerData();
        $customer->customer_id = $data['customerId'];
        $customer->customer_type = GetCustomerTypeService::getType($data['customerType']);
        $customer->customer_name = $data['customerName'];
        $customer->registration_no = $data['registrationNo'];
        $customer->vat_no = $data['vatNo'];
        $customer->tel = $data['tel'];
        $customer->alt_tel = $data['altTel'];
        $customer->email = $data['email'];
        $customer->web = $data['web'];
        $customer->search = $data['search'];

        return $customer;
    }
}
