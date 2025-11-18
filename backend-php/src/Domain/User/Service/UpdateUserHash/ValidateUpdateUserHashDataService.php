<?php

namespace App\Domain\User\Service\UpdateUserHash;

use App\Contract\DataValidationContract;
use App\Exception\ValidationException;
use App\Util\Utilities;

final class ValidateUpdateUserHashDataService implements DataValidationContract
{
    /**
     * @throws ValidationException
     */
    public function validateData(array $data): array
    {
        $fields = [];

        if (empty($data['password1'])) {
            $fields['password1'] = 'Invalid password provided.';
        }

        if (empty($data['password2'])) {
            $fields['password2'] = 'Invalid password provided.';
        }

        if ($data['password1'] !== $data['password2']) {
            $fields['password2'] = 'Password(s) do not match.';
        }

        if (count($fields) > 0) {
            throw new ValidationException('Data validation error.', 422, $fields);
        }

        return array_merge($data, [
            'userHash' => Utilities::passwordHash($data['password2'])
        ]);
    }
}
