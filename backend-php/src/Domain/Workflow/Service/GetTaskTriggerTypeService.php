<?php

namespace App\Domain\Workflow\Service;

use App\Domain\Workflow\Data\TaskTriggerType;

final class GetTaskTriggerTypeService
{
    public static function getTrigger(string $trigger_type): TaskTriggerType
    {
        return match (trim(strtolower($trigger_type))) {
            'automatic' => TaskTriggerType::AUTO,
            'manual' => TaskTriggerType::MANUAL,
            default => TaskTriggerType::NONE,
        };
    }
}
