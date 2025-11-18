<?php

namespace App\Domain\TaskMonitor\Service;

use App\Exception\ValidationException;

final class ValidateAddUpdateTaskMonitorDataService
{
    /**
     * @throws ValidationException
     */
    public static function validate(array $data): array
    {
        /*if (empty($data['tasks']) || count($data['tasks']) === 0) {
            throw new ValidationException('At least 1 (one) valid task is required to proceed');
        }*/

        $fields = [];
        if (empty($data['taskId'])) {
            $fields['taskId'] = 'Invalid task provided';
        }
        if (empty($data['escalationDays'])) {
            $fields['escalationDays'] = 'Invalid monitoring or escalation frequency provided';
        }
        if (empty($data['escalationType'])) {
            $fields['escalationType'] = 'Invalid escalation type provided';
        }
        if (count($fields) > 0) {
            throw new ValidationException('Data validation error', 422, $fields);
        }

        return $data;
    }
}
