<?php

namespace App\Contract;

interface SanitizeDataContract
{
    /**
     * Sanitize data function
     *
     * @param array $data
     * @return array
     */
    public static function sanitizeData(array $data): array;
}
