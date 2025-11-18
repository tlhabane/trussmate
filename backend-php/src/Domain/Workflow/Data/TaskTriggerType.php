<?php

namespace App\Domain\Workflow\Data;

enum TaskTriggerType: string
{
    case AUTO = 'automatic';
    case MANUAL = 'manual';
    case NONE = '';
}
