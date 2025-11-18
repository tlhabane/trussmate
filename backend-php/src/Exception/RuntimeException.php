<?php

namespace App\Exception;

use Exception;
use Throwable;

/**
 * Exception thrown if an error which can only be found on runtime occurs.
 * @link https://php.net/manual/en/class.runtimeexception.php
 */
class RuntimeException extends Exception
{
    public function __construct(string $message, int $code = 503, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
