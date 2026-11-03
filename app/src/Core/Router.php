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
use Salas\Controllers\TemaController;
use Salas\Controllers\UsoController;
use Salas\Controllers\UsuarioController;


/**
 * Classe Router.
 */
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
        'temas' => [TemaController::class, 'temasForm', 'query_post', 'merge', true],
        'estatisticas' => [RelatorioController::class, 'reportSummary', 'query', 'merge', true],
        'relatorios' => [RelatorioController::class, 'reportReservations', 'query', 'merge', true],
        'reserva_form' => [ReservaController::class, 'reservaForm', 'query', 'merge', false],
    ];


    private Database $db;

    /**
     * Método __construct.
     * @param Database $db
     * @return void
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public static function routeActions(): array
    {
        $configuredRoutes = include __DIR__ . '/../Config/routes.php';

        return array_values(array_unique(array_merge(
            array_keys(self::ROUTES),
            array_keys($configuredRoutes),
            ['dashboard', 'imagens_list']
        )));
    }

    /**
     * Método dispatch.
     * @param string $action
     * @param array $query
     * @param array $post
     * @return array
     */
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
        // Extract view name from route if '_view' key exists, otherwise use the route array
        $route = $routes[$action] ?? null;
        $view = '';
        if ($route !== null) {
            if (isset($route['_view'])) {
                $view = $route['_view'];
            } elseif (is_string($route)) {
                $view = $route;
            }
        }
        if ($view === '') {
            // Fallback: use the action name as view filename
            $view = str_replace('_', '', $action) . '.php';
        }

        return ['view' => $view, 'data' => $data];
    }

    /**
     * Método dashboard.
     * @param array $query
     * @return array
     */
    public function dashboard(array $query): array
    {
        (new AuthorizationMiddleware($this->db))->authorize('dashboard', $query, []);
        return [
            'dashboardView' => (new DashboardController($this->db))->index($query),
            'menuData' => $this->menuData(),
        ];
    }

    /**
     * Método menuData.
     * @return array
     */
    private function menuData(): array
    {
        $permissions = new AuthorizationService($this->db);
        $auth = new AuthContext();
        $level = $auth->level();
        $items = [
            ['action' => 'categoria_list', 'label' => 'Categorias', 'url' => '?acao=categoria_list'],
            ['action' => 'uso_list', 'label' => 'Usos', 'url' => '?acao=uso_list'],
            ['action' => 'sala_list', 'label' => 'Salas', 'url' => '?acao=sala_list'],
            ['action' => 'periodo_list', 'label' => 'Períodos', 'url' => '?acao=periodo_list'],
            ['action' => 'usuario_list', 'label' => 'Utilizadores', 'url' => '?acao=usuario_list'],
            ['action' => 'permissoes_form', 'label' => 'Permissões', 'url' => '?acao=permissoes_form'],
            ['action' => 'temas', 'label' => 'Temas', 'url' => '?acao=temas'],
        ];
        $visibleItems = [];
        foreach ($items as $item) {
            if ($permissions->validaRota($item['action'], $level, $auth->id())) {
                $visibleItems[] = $item;
            }
        }

        return [
            'userName' => $auth->name(),
            'userLevel' => $permissions->nomeNivel($level, 1),
            'profileUrl' => app_url('?acao=usuario_form&id=' . $auth->id()),
            'showProfile' => $permissions->perfilProprioAtivo(),
            'showStatistics' => $permissions->validaRota('estatisticas', $level, $auth->id()),
            'showReports' => $permissions->validaRota('relatorios', $level, $auth->id()),
            'adminItems' => $visibleItems,
        ];
    }
    /**
     * Método logout.
     * @return void
     */
    public function logout(): void
    {
        (new UsuarioController($this->db))->logout();
    }

    /**
     * Método login.
     * @param \Salas\Core\Http\Request $request
     * @return void
     */
    public function login(\Salas\Core\Http\Request $request): void
    {
        $loginView = (new LoginController($this->db))->handle($request->allQuery(), $request->allPost());
        if ($loginView['redirect'] !== null) {
            \Salas\Core\Http\Response::redirect($loginView['redirect'])->send();
            exit;
        }
        \Salas\Core\Http\ViewRenderer::render('Login.php', ['loginView' => $loginView]);
    }

    /**
     * Método imagensList.
     * @param \Salas\Core\Http\Request $request
     * @return void
     */
    public function imagensList(\Salas\Core\Http\Request $request): void
    {
        (new AuthorizationMiddleware($this->db))->authorize(
            'imagens_list',
            $request->allQuery(),
            $request->allPost()
        );
        $ctrl = new SalaController($this->db);
        header('Content-Type: application/json; charset=UTF-8');
        echo $ctrl->listarImagensJson($request->allQuery());
    }


}
