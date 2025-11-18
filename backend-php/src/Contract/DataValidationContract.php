<?php

namespace App\Contract;

use App\Exception\ValidationException;

interface DataValidationContract
{
    /**
     * Validate data function
     *
     * @param array $data
     * @throws ValidationException
     * @return array
     */
    public function validateData(array $data): array;
}
