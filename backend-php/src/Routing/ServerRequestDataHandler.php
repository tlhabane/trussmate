<?php

namespace App\Routing;

use App\Contract\ServerRequestContract;

class ServerRequestDataHandler implements ServerRequestContract
{
    private array $attributes;
    private array $queryParams;
    private array $headers;
    private array $files;

    public function __construct()
    {
        $this->setHeaders();
    }

    private function setHeaders(): void
    {
        $this->headers = [];
        $headers = getallheaders();
        foreach ($headers as $index => $header) {
            $headerIndex = strtolower(trim($index));
            $this->headers[$headerIndex] = $header;
        }
    }

    public function getHeaderLine(string $headerItemIndex): string
    {
        return $this->headers[$headerItemIndex] ?? '';
    }

    public function setQueryParams(array $queryParams): void
    {
        $this->queryParams[] = $queryParams;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams[0] ?? [];
    }

    public function setAttributes(mixed $attributes, string $attributeId = ''): void
    {
        if (empty($attributeId)) {
            $this->attributes[] = $attributes;
            return;
        }

        $this->attributes[$attributeId] = $attributes;
    }

    public function getAttributes(string $attributeId = ''): mixed
    {
        if (empty($attributeId)) {
            return $this->attributes ?? [];
        }
        return $this->attributes[$attributeId] ?? [];
    }

    public function setUploadedFiles(array $files): void
    {
        $this->files = $files;
    }

    public function getUploadedFiles(): array
    {
        return $this->files ?? [];
    }
}
