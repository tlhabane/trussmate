<?php

namespace App\Exception;

use Throwable;

final class ValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(string $message, int $code = 422, array $errors = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
