<?php
namespace Salas\Tests\Integration;

use Salas\Controllers\LoginController;
use Salas\Core\Auth\AuthorizationService;
use Salas\Models\Permissoes;
use Salas\Models\Usuario;

class PublicRegistrationIntegrationTest extends DatabaseTestCase
{
    private ?array $previousSession = null;
    private bool $hadSession = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadSession = isset($_SESSION);
        $this->previousSession = $_SESSION ?? null;
        $_SESSION = [];
        (new Permissoes($this->db))->ensureDefaults();
    }

    protected function tearDown(): void
    {
        if ($this->hadSession) {
            $_SESSION = $this->previousSession ?? [];
        } else {
            unset($_SESSION);
        }
        parent::tearDown();
    }

    public function testRegistrationPermissionControlsLinkAndDirectAccess(): void
    {
        $this->setPublicRegistration(false);
        $controller = new LoginController($this->db);

        $loginView = $controller->handle(['registar' => 'registar'], []);
        $this->assertFalse($loginView['registoPublico']);
        $this->assertFalse($loginView['registar']);
        $this->assertStringContainsString('desativado', $loginView['errormsg']);

        $registrationResult = $controller->handle([], [
            'validar' => 'Validar',
            'id' => '0',
            'nome' => 'blocked_' . uniqid(),
            'email' => uniqid() . '@example.test',
            'senha' => 'password',
        ]);
        $this->assertFalse($registrationResult['validou']);
        $this->assertFalse($registrationResult['registar']);

        $this->setPublicRegistration(true);
        $loginView = $controller->handle(['registar' => 'registar'], []);
        $this->assertTrue($loginView['registoPublico']);
        $this->assertTrue($loginView['registar']);
    }

    public function testSuccessfulRegistrationIsReportedOnlyForInsertedUser(): void
    {
        $this->setPublicRegistration(true);
        $suffix = uniqid();

        $result = (new LoginController($this->db))->handle([], [
            'validar' => 'Validar',
            'id' => '0',
            'nome' => 'new_' . $suffix,
            'email' => 'new_' . $suffix . '@example.test',
            'senha' => 'password',
        ]);

        $this->assertTrue($result['validou']);
        $this->assertSame('Utilizador criado com sucesso!', $result['errormsg']);
    }

    public function testDuplicateUsernameIsRejectedEvenWithDifferentEmail(): void
    {
        $suffix = uniqid();
        $existingName = 'existing_' . $suffix;
        $users = new Usuario($this->db);
        $this->assertGreaterThan(
            0,
            $users->salvar(0, $existingName, 'first_' . $suffix . '@example.test', 'password', '', '', 0)
        );

        $result = (new LoginController($this->db))->handle([], [
            'validar' => 'Validar',
            'id' => '0',
            'nome' => $existingName,
            'email' => 'second_' . $suffix . '@example.test',
            'senha' => 'password',
        ]);

        $this->assertFalse($result['validou']);
        $this->assertNotSame('Utilizador criado com sucesso!', $result['errormsg']);
    }

    public function testDuplicateEmailIsRejectedEvenWithDifferentUsername(): void
    {
        $suffix = uniqid();
        $existingEmail = 'existing_' . $suffix . '@example.test';
        $users = new Usuario($this->db);
        $this->assertGreaterThan(
            0,
            $users->salvar(0, 'existing_' . $suffix, $existingEmail, 'password', '', '', 0)
        );

        $result = (new LoginController($this->db))->handle([], [
            'validar' => 'Validar',
            'id' => '0',
            'nome' => 'another_' . $suffix,
            'email' => $existingEmail,
            'senha' => 'password',
        ]);

        $this->assertFalse($result['validou']);
        $this->assertNotSame('Utilizador criado com sucesso!', $result['errormsg']);
    }

    private function setPublicRegistration(bool $enabled): void
    {
        (new Permissoes($this->db))->update(
            null,
            'A_RegNovoUtilizador',
            null,
            $enabled,
            null,
            null,
            'Update'
        );

        $authorization = new AuthorizationService($this->db);
        $this->assertSame($enabled, $authorization->registoPublicoAtivo());
    }
}
