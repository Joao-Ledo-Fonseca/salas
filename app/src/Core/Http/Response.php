<?php

declare(strict_types=1);

namespace Salas\Core\Http;


/**
 * Classe Response.
 */
class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $content = '';

    /**
     * Método __construct.
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     * @return void
     */
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Método html.
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     * @return self
     */
    public static function html(string $content, int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, array_merge(['Content-Type' => 'text/html; charset=UTF-8'], $headers));
    }

    /**
     * Método text.
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     * @return self
     */
    public static function text(string $content, int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, array_merge(['Content-Type' => 'text/plain; charset=UTF-8'], $headers));
    }

    /**
     * Método redirect.
     * @param string $url
     * @param int $statusCode
     * @param array $headers
     * @return self
     */
    public static function redirect(string $url, int $statusCode = 302, array $headers = []): self
    {
        return new self('', $statusCode, array_merge(['Location' => $url], $headers));
    }

    /**
     * Método withStatus.
     * @param int $statusCode
     * @return self
     */
    public function withStatus(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Método withHeader.
     * @param string $name
     * @param string $value
     * @return self
     */
    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Método getStatusCode.
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Método getHeaders.
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Método getContent.
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Método output.
     * @return string
     */
    public function output(): string
    {
        return $this->content;
    }

    /**
     * Método send.
     * @return void
     */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        echo $this->output();
    }
}
