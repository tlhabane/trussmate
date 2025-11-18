<?php

namespace App\Util;

use App\Contract\TelephoneValidationContract;
use App\Contract\EmailValidationContract;

class ValidateExistingContactUtility
{
    private TelephoneValidationContract $telephoneValidator;
    private EmailValidationContract $emailValidator;

    public function __construct(
        TelephoneValidationContract $telephoneValidator,
        EmailValidationContract $emailValidator
    )
    {
        $this->emailValidator = $emailValidator;
        $this->telephoneValidator = $telephoneValidator;
    }

    public function validateContact(array $data, array $existingData): array
    {
        $fields = [];

        $tel_update = (
            Utilities::removeNonDigits($existingData['tel']) !== Utilities::removeNonDigits($data['tel'])
        );
        if (empty($data['tel'])) {
            $fields['tel'] = 'Invalid telephone number provided.';
        } else if ($tel_update && $this->telephoneValidator->telExists($data['tel'])) {
            $fields['tel'] = sprintf('%s: is already registered.', $data['tel']);
        }

        if (!empty($data['altTel'])) {
            $alt_tel_update = (
                Utilities::removeNonDigits($existingData['altTel']) !==
                Utilities::removeNonDigits($data['altTel'])
            );
            if ($alt_tel_update && $this->telephoneValidator->telExists($data['altTel'])) {
                $fields['altTel'] = sprintf('%s: is already registered.', $data['altTel']);
            }
        }

        $email_update = $existingData['email'] !== $data['email'];
        if (empty($data['email'])) {
            $fields['email'] = 'Invalid email address provided.';
        } else if ($email_update && $this->emailValidator->emailExists($data['email'])) {
            $fields['email'] = sprintf('%s: is already registered.', $data['email']);
        }

        return $fields;
    }
}
