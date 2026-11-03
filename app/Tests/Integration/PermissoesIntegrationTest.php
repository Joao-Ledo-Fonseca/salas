<?php
namespace Salas\Tests\Integration;

use Salas\Core\Auth\AuthorizationMiddleware;
use Salas\Core\Router;
use Salas\Core\Auth\AuthorizationService;
use Salas\Models\Permissoes;

class PermissoesIntegrationTest extends DatabaseTestCase
{
    private ?array $previousSession = null;
    private bool $hadSession = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hadSession = isset($_SESSION);
        $this->previousSession = $_SESSION ?? null;
        $_SESSION = [];
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

    public function testListar(): void
    {
        $m = new Permissoes($this->db);
        $this->assertIsArray($m->listar('n'));
    }

    public function testEnsureDefaults(): void
    {
        $m = new Permissoes($this->db);
        $m->ensureDefaults();
        $this->assertIsArray($m->listar());
    }

    public function testAuthorizationDoesNotInitializeMissingPermissionRows(): void
    {
        $permissions = new Permissoes($this->db);
        $permissions->excluir('M_Usos');

        $authorization = new AuthorizationService($this->db);

        $this->assertTrue($authorization->validaPermissao('M_Usos', 1));
        $this->assertSame(
            [],
            array_values(array_filter(
                $permissions->listar(),
                static fn (array $row): bool => $row['nome'] === 'M_Usos'
            ))
        );
    }

    public function testPermissionChangesAreReadDespiteStaleSessionCache(): void
    {
        $permissions = new Permissoes($this->db);
        $permissions->ensureDefaults();
        $permissions->update(null, 'M_Categorias', null, true, false, false, 'Update');

        $_SESSION['auth_permissions'] = ['M_Categorias' => [0, 0, 0]];
        $authorization = new AuthorizationService($this->db);
        $this->assertTrue($authorization->validaPermissao('M_Categorias', 0));

        $permissions->update(null, 'M_Categorias', null, false, false, false, 'Update');
        $authorization = new AuthorizationService($this->db);
        $this->assertFalse($authorization->validaPermissao('M_Categorias', 0));
    }

    public function testConfiguredRoutesAllowAndDenyAccessByPermission(): void
    {
        $permissions = new Permissoes($this->db);
        $permissions->ensureDefaults();
        $permissions->update(null, 'M_Categorias', null, false, true, false, 'Update');
        $permissions->update(null, 'M_Permissoes', null, false, false, true, 'Update');
        $permissions->update(null, 'M_Utilizadores', null, false, false, false, 'Update');
        $permissions->update(null, 'A_PerfilUtilizador', null, true, null, null, 'Update');

        $middleware = new AuthorizationMiddleware($this->db);
        $this->assertTrue($middleware->isAllowed('categoria_list', 10, 1));
        $this->assertFalse($middleware->isAllowed('permissoes_form', 10, 1));
        $this->assertTrue($middleware->isAllowed('dashboard', 10, 0));
        $this->assertTrue($middleware->isAllowed('imagens_list', 10, 1));
        $this->assertFalse($middleware->isAllowed('imagens_list', 10, 0));
        $this->assertTrue($middleware->isAllowed('usuario_form', 10, 0, ['id' => 10]));
        $this->assertFalse($middleware->isAllowed('usuario_form', 10, 0, ['id' => 11]));
    }

    public function testOwnProfilePermissionControlsSelfEditingRoute(): void
    {
        $permissions = new Permissoes($this->db);
        $permissions->ensureDefaults();
        $permissions->update(null, 'M_Utilizadores', null, true, true, true, 'Update');
        $permissions->update(null, 'A_PerfilUtilizador', null, true, null, null, 'Update');

        $middleware = new AuthorizationMiddleware($this->db);
        $this->assertTrue($middleware->isAllowed('usuario_form', 10, 0, ['id' => 10]));

        $permissions->update(null, 'A_PerfilUtilizador', null, false, null, null, 'Update');

        $this->assertFalse($middleware->isAllowed('usuario_form', 10, 0, ['id' => 10]));
        $this->assertFalse($middleware->isAllowed('usuario_form', 10, 3, ['id' => 10]));
        $this->assertTrue($middleware->isAllowed('usuario_form', 11, 1, ['id' => 10]));
    }

    public function testUnknownPermissionAndInvalidLevelAreDenied(): void
    {
        $authorization = new AuthorizationService($this->db);

        $this->assertFalse($authorization->validaPermissao('M_NaoConfigurada', 3));
        $this->assertFalse($authorization->validaPermissao('M_Categorias', -1));
        $this->assertFalse($authorization->validaPermissao('M_Categorias', 4));
        $this->assertFalse($authorization->validaRota('unknown_route', 3));
    }

    public function testUnmappedRouteIsDeniedAndReported(): void
    {
        $middleware = new AuthorizationMiddleware($this->db);

        $this->assertFalse($middleware->isAllowed('future_route', 10, 3));
        $this->assertSame(
            ['future_route' => 'Sem permissão configurada.'],
            AuthorizationService::routeWarnings(['future_route'])
        );
    }

    public function testAllCurrentRoutesHaveValidPermissionMappings(): void
    {
        $this->assertSame(
            [],
            AuthorizationService::routeWarnings(Router::routeActions())
        );
    }
}
?>