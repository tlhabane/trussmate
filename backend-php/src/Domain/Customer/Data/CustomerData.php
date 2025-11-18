<?php

namespace App\Domain\Customer\Data;

class CustomerData
{
    /* FK: Customer info owner */
    public string $account_no;
    /* Customer info */
    public string $customer_id;
    public CustomerType $customer_type;
    public string $customer_name;
    public string $registration_no;
    public string $vat_no;
    public string $tel;
    public string $alt_tel;
    public string $email;
    public string $web;
    /* Full text search */
    public string $search;
}
