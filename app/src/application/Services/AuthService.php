<?php
require_once __DIR__ . '/../../infrastructure/Validation/Validator.php';

require_once __DIR__ . '/../../infrastructure/Auth/Authenticator.php';
require_once __DIR__ . '/../../infrastructure/Auth/SalasCredentialRepository.php';
require_once __DIR__ . '/../../infrastructure/Auth/SalasSessionAdapter.php';
require_once __DIR__ . '/../../infrastructure/Auth/AuthContext.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../infrastructure/Support/Util.php';
require_once __DIR__ . '/../../controllers/UsuarioController.php';

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
        $authenticator = new \Salas\Auth\Authenticator(
            new \Salas\Integration\SalasCredentialRepository($usuario)
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

