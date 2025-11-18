<?php

namespace App\Domain\Task\Data;

enum TaskPaymentType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
    case NONE = '0';
}
