<?php
namespace Salas\Core\Auth;


class AuthContext
{
    private \Salas\Core\Auth\SessionManager $sessions;
    private \Salas\Core\Auth\SalasSessionAdapter $adapter;

    public function __construct(
        ?\Salas\Core\Auth\SessionManager $sessions = null,
        ?\Salas\Core\Auth\SalasSessionAdapter $adapter = null
    ) {
        $this->sessions = $sessions ?? new \Salas\Core\Auth\SessionManager();
        $this->adapter = $adapter ?? new \Salas\Core\Auth\SalasSessionAdapter($this->sessions);
    }

    public function start(): void
    {
        $this->adapter->start();
    }

    public function isAuthenticated(): bool
    {
        $this->start();
        return $this->adapter->isAuthenticated();
    }

    public function identity(): ?array
    {
        $this->start();
        $identity = $this->sessions->identity();
        if (is_array($identity)) {
            return $identity;
        }

        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'nome' => $_SESSION['user_nome'] ?? '',
                'nivel' => $_SESSION['user_nivel'] ?? 0,
                'email' => '',
            ];
        }

        return null;
    }

    public function id(): int
    {
        return (int) ($this->identity()['id'] ?? 0);
    }

    public function name(): string
    {
        return (string) ($this->identity()['nome'] ?? '');
    }

    public function level(): int
    {
        return (int) ($this->identity()['nivel'] ?? -1);
    }

    public function login(array $identity): void
    {
        $this->adapter->login($identity);
    }

    public function logout(): void
    {
        $this->adapter->logout();
    }
}

