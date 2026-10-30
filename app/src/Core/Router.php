<?php
namespace Salas\Core;

use Salas\Core\Auth\AuthorizationMiddleware;
use Salas\Core\Auth\AuthContext;
use Salas\Core\Auth\AuthorizationService;
use Salas\Core\Persistence\Database;
use Salas\Core\Support\Csrf;
use Salas\Controllers\CategoriaController;
use Salas\Controllers\DashboardController;
use Salas\Controllers\LoginController;
use Salas\Controllers\PeriodoController;
use Salas\Controllers\PermissoesController;
use Salas\Controllers\RelatorioController;
use Salas\Controllers\ReservaController;
use Salas\Controllers\SalaController;
use Salas\Controllers\UsoController;
use Salas\Controllers\UsuarioController;


class Router
{
    private const ROUTES = [
        'categoria_list' => [CategoriaController::class, 'listarController', 'none', 'lista', true],
        'categoria_form' => [CategoriaController::class, 'categoriaForm', 'query_post', 'merge', true],
        'uso_list' => [UsoController::class, 'listarController', 'none', 'lista', true],
        'uso_form' => [UsoController::class, 'usoForm', 'query_post', 'merge', true],
        'periodo_list' => [PeriodoController::class, 'listarController', 'none', 'lista', true],
        'periodo_form' => [PeriodoController::class, 'periodoForm', 'query_post', 'merge', true],
        'sala_list' => [SalaController::class, 'salaList', 'query_post', 'merge', true],
        'sala_form' => [SalaController::class, 'salaForm', 'query_post', 'merge', true],
        'usuario_list' => [UsuarioController::class, 'listarController', 'none', 'lista', true],
        'usuario_form' => [UsuarioController::class, 'usuarioForm', 'query_post', 'merge', true],
        'permissoes_form' => [PermissoesController::class, 'permissoesForm', 'post', 'merge', true],
        'estatisticas' => [RelatorioController::class, 'reportSummary', 'query', 'merge', false],
        'relatorios' => [RelatorioController::class, 'reportReservations', 'query', 'merge', false],
        'reserva_form' => [ReservaController::class, 'reservaForm', 'query', 'merge', false],
    ];


    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function dispatch(string $action, array $query, array $post): array
    {
        if ($post !== [] && !Csrf::validate($post['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('CSRF token inválido.');
        }
        (new AuthorizationMiddleware($this->db))->authorize($action, $query, $post);

        if ($action === 'reserva_form' && isset($post['submeter_excluir'])) {
            return ['response' => (new ReservaController($this->db))->excluir($post)];
        }
        if ($action === 'reserva_form' && isset($post['submeter_salvar'])) {
            return ['response' => (new ReservaController($this->db))->salvar($post)];
        }

        $data = ['menuData' => $this->menuData()];

        $route = self::ROUTES[$action] ?? null;
        if ($route === null) {
            throw new \RuntimeException('Ação não suportada: ' . $action);
        }

        [$controllerClass, $method, $args, $assign, $layout] = $route;
        $controller = new $controllerClass($this->db);

        if ($args === 'query_post') {
            $result = $controller->{$method}($query, $post);
        } elseif ($args === 'post') {
            $result = $controller->{$method}($post);
        } elseif ($args === 'query') {
            $result = $controller->{$method}($query);
        } else {
            $result = $controller->{$method}();
        }

        if ($assign === 'lista') {
            $data['lista'] = $result;
        } else {
            $data += $result;
        }

        if ($layout) {
            $data['_layout'] = 'layout.php';
        }

        if ($action === 'usuario_list') {
            $permissionController = new AuthorizationService($this->db);
            $users = $data['lista'];
            foreach (array_keys($users) as $i) {
                $user = $users[$i];
                $user['nivel_nome'] = $permissionController->nomeNivel((int) $user['nivel'], 2);
                $users[$i] = $user;
            }
            $data['lista'] = $users;
        }

        $routes = include __DIR__ . '/../config/routes.php';
        return ['view' => $routes[$action], 'data' => $data];
    }

    public function dashboard(array $query): array
    {
        (new AuthorizationMiddleware($this->db))->authorize('dashboard', $query, []);
        return [
            'dashboardView' => (new DashboardController($this->db))->index($query),
            'menuData' => $this->menuData(),
        ];
    }

    private function menuData(): array
    {
        $permissions = new AuthorizationService($this->db);
        $auth = new AuthContext();
        $level = $auth->level();
        $items = [
            ['permission' => 'M_Categorias', 'label' => 'Categorias', 'url' => '?acao=categoria_list'],
            ['permission' => 'M_Usos', 'label' => 'Usos', 'url' => '?acao=uso_list'],
            ['permission' => 'M_Salas', 'label' => 'Salas', 'url' => '?acao=sala_list'],
            ['permission' => 'M_Periodos', 'label' => 'Períodos', 'url' => '?acao=periodo_list'],
            ['permission' => 'M_Utilizadores', 'label' => 'Utilizadores', 'url' => '?acao=usuario_list'],
            ['permission' => 'M_Permissoes', 'label' => 'Permissões', 'url' => '?acao=permissoes_form'],
        ];
        $visibleItems = [];
        foreach ($items as $item) {
            if ($permissions->validaPermissao($item['permission'], $level)) {
                $visibleItems[] = $item;
            }
        }

        return [
            'userName' => $auth->name(),
            'userLevel' => $permissions->nomeNivel($level, 1),
            'profileUrl' => app_url('?acao=usuario_form&id=' . $auth->id()),
            'showStatistics' => $permissions->validaPermissao('M_Estatisticas', $level),
            'showReports' => $permissions->validaPermissao('M_Relatorios', $level),
            'adminItems' => $visibleItems,
        ];
    }
    public function logout(): void
    {
        (new UsuarioController($this->db))->logout();
    }

    public function login(\Salas\Core\Http\Request $request): void
    {
        $loginView = (new LoginController($this->db))->handle($request->allQuery(), $request->allPost());
        if ($loginView['redirect'] !== null) {
            \Salas\Core\Http\Response::redirect($loginView['redirect'])->send();
            exit;
        }
        \Salas\Core\Http\ViewRenderer::render('Login.php', ['loginView' => $loginView]);
    }

    public function imagensList(\Salas\Core\Http\Request $request): void
    {
        $ctrl = new SalaController($this->db);
        header('Content-Type: application/json; charset=UTF-8');
        echo $ctrl->listarImagensJson($request->allQuery());
    }


}

