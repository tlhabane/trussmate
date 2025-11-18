<?php

namespace App\Domain\SaleTask\Data;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case STARTED = 'started';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case TENTATIVE = '';
}
