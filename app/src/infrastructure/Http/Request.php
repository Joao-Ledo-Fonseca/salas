<?php

declare(strict_types=1);

namespace Salas\Infrastructure\Http;


final class Request
{
    public function __construct(
        private array $query = [],
        private array $post = [],
        private array $files = [],
        private array $server = [],
        private array $session = []
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self(
            $_GET ?? [],
            $_POST ?? [],
            $_FILES ?? [],
            $_SERVER ?? [],
            $_SESSION ?? []
        );
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function allQuery(): array
    {
        return $this->query;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function allPost(): array
    {
        return $this->post;
    }

    public function file(string $key, mixed $default = null): mixed
    {
        return $this->files[$key] ?? $default;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    public function session(string $key, mixed $default = null): mixed
    {
        return $this->session[$key] ?? $default;
    }

    public function isAjax(): bool
    {
        return strtolower((string) ($this->server['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    public function action(): ?string
    {
        $action = $this->query('acao');
        return is_string($action) && $action !== '' ? $action : null;
    }
}
