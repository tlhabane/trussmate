<?php

namespace App\Domain\Task\Service\AddTask;

use App\Exception\ValidationException;

final class ValidateAddTaskDataService
{
    /**
     * @throws ValidationException
     */
    public static function validateData(array $data): array
    {
        if (empty($data['taskName'])) {
            throw new ValidationException('Data validation error', 422, [
               'taskName' => 'Invalid task name provided'
            ]);
        }

        return $data;
    }
}
