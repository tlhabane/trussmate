<?php

namespace App\Domain\UserSession\Service\TerminateSession;

use App\Contract\DataValidationContract;
use App\Domain\UserSession\Data\SessionStatus;
use App\Exception\ValidationException;

final class ValidateTerminateSessionDataService implements DataValidationContract
{
    public function validateData(array $data): array
    {
        if (empty($data['sessionId'])) {
            throw new ValidationException('Invalid or expired session, please login again.', 401);
        }
        $data['sessionStatus'] = $data['sessionStatus'] ?? SessionStatus::ended->name;

        return $data;
    }
}
