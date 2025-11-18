<?php

namespace App\Util;

use App\Contract\TelephoneValidationContract;
use App\Contract\EmailValidationContract;

class ValidateContactUtility
{
    private TelephoneValidationContract $telephoneValidator;
    private EmailValidationContract $emailValidator;

    public function __construct(
        TelephoneValidationContract $telephoneValidator,
        EmailValidationContract $emailValidator
    ) {
        $this->emailValidator = $emailValidator;
        $this->telephoneValidator = $telephoneValidator;
    }

    public function validateContact(array $data): array
    {
        $fields = [];

        if (empty($data['tel'])) {
            $fields['tel'] = 'Invalid telephone number provided.';
        } elseif ($this->telephoneValidator->telExists($data['tel'])) {
            $fields['tel'] = sprintf('%s: is already registered.', $data['tel']);
        }

        if (!empty($data['altTel']) && $this->telephoneValidator->telExists($data['altTel'])) {
            $fields['altTel'] = sprintf('%s: is already registered.', $data['altTel']);
        }

        if (empty($data['email'])) {
            $fields['email'] = 'Invalid email address provided.';
        } elseif ($this->emailValidator->emailExists($data['email'])) {
            $fields['email'] = sprintf('%s: is already registered.', $data['email']);
        }

        return $fields;
    }
}
