<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Data\TaskAction;

final class GetTaskActionService
{
    public static function getAction(string $task_action): TaskAction
    {
        return match (trim(strtolower($task_action))) {
            'task' => TaskAction::TASK,
            'invoice' => TaskAction::INVOICE,
            'penalty' => TaskAction::PENALTY,
            'proforma', 'proforma_invoice' => TaskAction::PROFORMA,
            'quotation' => TaskAction::QUOTATION,
            'estimate' => TaskAction::ESTIMATE,
            default => TaskAction::NONE
        };
    }
}
