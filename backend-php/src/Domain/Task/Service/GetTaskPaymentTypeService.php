<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Data\TaskPaymentType;

final class GetTaskPaymentTypeService
{
    public static function getType(string $type): TaskPaymentType
    {
        return match (trim(strtolower($type))) {
            'fixed' => TaskPaymentType::FIXED,
            'percentage' => TaskPaymentType::PERCENTAGE,
            default => TaskPaymentType::NONE,
        };
    }
}
