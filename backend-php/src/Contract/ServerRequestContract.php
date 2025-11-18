<?php

namespace App\Contract;

interface ServerRequestContract
{
    public function setQueryParams(array $queryParams): void;
    public function getQueryParams(): array;
    public function setAttributes(mixed $attributes, string $attributeId = ''): void;
    public function getAttributes(string $attributeId = ''): mixed;
}
