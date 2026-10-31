<?php

declare(strict_types=1);

namespace Salas\Core\Http;


/**
 * Classe Request.
 */
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

    /**
     * Método fromGlobals.
     * @return self
     */
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

    /**
     * Método query.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Método allQuery.
     * @return array
     */
    public function allQuery(): array
    {
        return $this->query;
    }

    /**
     * Método post.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Método allPost.
     * @return array
     */
    public function allPost(): array
    {
        return $this->post;
    }

    /**
     * Método file.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function file(string $key, mixed $default = null): mixed
    {
        return $this->files[$key] ?? $default;
    }

    /**
     * Método server.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Método session.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function session(string $key, mixed $default = null): mixed
    {
        return $this->session[$key] ?? $default;
    }

    /**
     * Método isAjax.
     * @return bool
     */
    public function isAjax(): bool
    {
        return strtolower((string) ($this->server['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    /**
     * Método action.
     * @return ?string
     */
    public function action(): ?string
    {
        $action = $this->query('acao');
        return is_string($action) && $action !== '' ? $action : null;
    }
}
