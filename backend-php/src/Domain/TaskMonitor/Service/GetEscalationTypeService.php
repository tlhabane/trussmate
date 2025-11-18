<?php

namespace App\Domain\TaskMonitor\Service;

use App\Domain\TaskMonitor\Data\EscalationType;

final class GetEscalationTypeService
{
    public static function getType(string $escalation_type): EscalationType
    {
        return match (strtolower(trim($escalation_type))) {
            'progress' => EscalationType::PROGRESS,
            'overdue' => EscalationType::OVERDUE,
            default => EscalationType::NONE
        };
    }
}
