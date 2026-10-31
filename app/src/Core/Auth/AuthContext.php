<?php
namespace Salas\Core\Auth;


/**
 * Classe AuthContext.
 */
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

    /**
     * Método start.
     * @return void
     */
    public function start(): void
    {
        $this->adapter->start();
    }

    /**
     * Método isAuthenticated.
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        $this->start();
        return $this->adapter->isAuthenticated();
    }

    /**
     * Método identity.
     * @return ?array
     */
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

    /**
     * Método id.
     * @return int
     */
    public function id(): int
    {
        return (int) ($this->identity()['id'] ?? 0);
    }

    /**
     * Método name.
     * @return string
     */
    public function name(): string
    {
        return (string) ($this->identity()['nome'] ?? '');
    }

    /**
     * Método level.
     * @return int
     */
    public function level(): int
    {
        return (int) ($this->identity()['nivel'] ?? -1);
    }

    /**
     * Método login.
     * @param array $identity
     * @return void
     */
    public function login(array $identity): void
    {
        $this->adapter->login($identity);
    }

    /**
     * Método logout.
     * @return void
     */
    public function logout(): void
    {
        $this->adapter->logout();
    }
}

