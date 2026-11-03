<?php
namespace Salas\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Salas\Controllers\PermissoesController;
use Salas\Controllers\UsuarioController;
use Salas\Core\Auth\AuthorizationService;
use Salas\Core\Persistence\Database;
use Salas\Models\Permissoes;

class PermissionPhaseOneTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->previousSession = $_SESSION;
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function testAssignedLevelCannotExceedActorLevel(): void
    {
        $this->assertTrue(AuthorizationService::canAssignLevel(1, 1));
        $this->assertTrue(AuthorizationService::canAssignLevel(3, 3));
        $this->assertFalse(AuthorizationService::canAssignLevel(1, 2));
        $this->assertFalse(AuthorizationService::canAssignLevel(2, 3));
    }

    public function testAssignedLevelMustBeKnownAndActorLevelValid(): void
    {
        $this->assertFalse(AuthorizationService::canAssignLevel(-1, 0));
        $this->assertFalse(AuthorizationService::canAssignLevel(4, 0));
        $this->assertFalse(AuthorizationService::canAssignLevel(2, -1));
        $this->assertFalse(AuthorizationService::canAssignLevel(2, 4));
    }

    public function testUserControllerRejectsRequestToAssignHigherLevel(): void
    {
        $_SESSION['user_id'] = 10;
        $_SESSION['user_nivel'] = 1;
        $controller = new UsuarioController($this->createMock(Database::class));

        $result = $controller->salvar([
            'salvar' => 'Salvar',
            'id' => '11',
            'nome' => 'Utilizador',
            'email' => 'utilizador@example.test',
            'nivel' => '2',
        ]);

        $this->assertFalse($result);
    }

    public function testPermissionFormResetsUncheckedOptionsAndIgnoresUnknownFields(): void
    {
        $result = PermissoesController::permissoesDoFormulario([
            'M_Salas0' => '0',
            'M_Salas2' => '2',
            'A_PerfilUtilizador0' => '0',
            'A_PerfilUtilizador2' => '2',
            'unknown0' => '0',
        ]);

        $this->assertCount(count(Permissoes::DEFAULTS), $result);
        $this->assertSame([true, false, true], $result['M_Salas']);
        $this->assertSame([true, false, false], $result['A_PerfilUtilizador']);
        $this->assertSame([false, false, false], $result['M_Categorias']);
        $this->assertArrayNotHasKey('unknown', $result);
    }
}
