<?php

namespace App\Contract;

interface TelephoneValidationContract
{
    /**
     * Validate if telephone number exists
     *
     * @param string $telephone
     * @return bool
     * */
    public function telExists(string $telephone): bool;
}
