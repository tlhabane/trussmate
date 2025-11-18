<?php

namespace App\Domain\TaskMonitor\Data;

enum EscalationType: string
{
    case PROGRESS = 'progress';
    case OVERDUE = 'overdue';
    case NONE = '';
}
