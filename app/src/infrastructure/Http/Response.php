<?php

declare(strict_types=1);

namespace Salas\Infrastructure\Http;


class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function html(string $content, int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, array_merge(['Content-Type' => 'text/html; charset=UTF-8'], $headers));
    }

    public static function text(string $content, int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, array_merge(['Content-Type' => 'text/plain; charset=UTF-8'], $headers));
    }

    public static function redirect(string $url, int $statusCode = 302, array $headers = []): self
    {
        return new self('', $statusCode, array_merge(['Location' => $url], $headers));
    }

    public function withStatus(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        echo $this->content;
    }
}
