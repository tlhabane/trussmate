<?php

namespace App\Routing;

class ServerResponseHandler
{
    private int $status;
    private string $statusText;
    private array $headers = [
        'Content-Type' => 'application/json'
    ];

    public function write(string $response): ServerResponseHandler
    {
        // ob_clean();
        header_remove();

        if (isset($this->headers)) {
            foreach ($this->headers as $key => $header) {
                header("{$key}: {$header}");
            }
        }

        $httpCode = $this->status ?? 200;
        $statusText = empty($this->statusText) ? '' : ' ' . $this->statusText;
        header("HTTP/1.1 {$httpCode}{$statusText}");

        echo $response;

        return $this;
    }

    public function withStatus(int $status, string $statusText = ''): ServerResponseHandler
    {
        $this->status = $status;
        $this->statusText = $statusText;
        return $this;
    }

    public function withHeader(string $key, string $value): ServerResponseHandler
    {
        $this->headers[$key] = $value;
        return $this;
    }
}
