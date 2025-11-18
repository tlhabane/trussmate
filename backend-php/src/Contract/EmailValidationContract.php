<?php

namespace App\Contract;

interface EmailValidationContract
{
    /**
     * Validate if email address exists
     *
     * @param string $email_address
     * @return bool
     * */
    public function emailExists(string $email_address): bool;
}
