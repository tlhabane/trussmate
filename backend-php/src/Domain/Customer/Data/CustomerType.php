<?php

namespace App\Domain\Customer\Data;

enum CustomerType: string
{
    case business = 'business';
    case individual = 'individual';
    case all = '';
}
