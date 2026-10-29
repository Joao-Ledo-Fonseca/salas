<?php
namespace Salas\Application\Services;


use Salas\Infrastructure\Auth\AuthContext;
use Salas\Infrastructure\Persistence\Database;
use Salas\Models\Usuario;
use Salas\Controllers\UsuarioController;
use Salas\Infrastructure\Validation\Validator;

class AuthService
{
    private Database $db;
    private AuthContext $auth;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->auth = new AuthContext();
    }

    public function authenticate(string $identifier, string $password): ?array
    {
        $usuario = new Usuario($this->db);
        $authenticator = new \Salas\Infrastructure\Auth\Authenticator(
            new \Salas\Infrastructure\Auth\SalasCredentialRepository($usuario)
        );

        return $authenticator->authenticate(
            Validator::string($identifier),
            $password
        );
    }

    public function login(array $identity): void
    {
        $this->auth->login($identity);
    }

    public function logout(): void
    {
        $this->auth->logout();
    }

    public function register(array $post): int
    {
        $controller = new UsuarioController($this->db);
        return (int) $controller->salvar($post);
    }

    public function isAuthenticated(): bool
    {
        return $this->auth->isAuthenticated();
    }

    public function identity(): ?array
    {
        return $this->auth->identity();
    }
}

