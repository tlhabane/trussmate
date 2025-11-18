<?php

namespace App\Domain\Sale\Data;

enum SaleStatus: string
{
    case PENDING = 'pending';
    case STARTED = 'started';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case TENTATIVE = '';
}
