<?php

namespace App\Domain\UserSession\Service\AddSession;

use App\Contract\DataValidationContract;
use App\Exception\ValidationException;

final class ValidateAddSessionDataService implements DataValidationContract
{
    public function validateData(array $data): array
    {
        if (empty($data['accountNo'])) {
            throw new ValidationException('Invalid account no. provided');
        }

        if (empty($data['username'])) {
            throw new ValidationException('Invalid username provided');
        }

        if (empty($data['userRole'])) {
            throw new ValidationException('Invalid username provided');
        }

        return $data;
    }
}
