<?php



$projectRoot = dirname(__DIR__);
$appRoot = $projectRoot . DIRECTORY_SEPARATOR . 'src';

require_once $appRoot . '/../src/config/bootstrap.php';

use Salas\Infrastructure\Persistence\Database;
use Salas\Application\Services\DefaultSuperAdminSeeder;
use Salas\Infrastructure\Http\Request;
use Salas\Infrastructure\Http\Response;
use Salas\Infrastructure\Http\ErrorHandler;
use Salas\Infrastructure\Http\ViewRenderer;
use Salas\Infrastructure\Auth\SalasSessionAdapter;
use Salas\Controllers\LoginController;
use Salas\Controllers\UsuarioController;
use Salas\Application\Router;

$database = new Database($appConfig);

(new DefaultSuperAdminSeeder($database))->seed();

ErrorHandler::register();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

$sessionAdapter = new SalasSessionAdapter();
$sessionAdapter->start();

$request = Request::fromGlobals();

// 🎯 CONFIGURAÇÃO: Tempo limite de inatividade (ex: 1800s = 30 minutos)
$tempo_limite = 600; 

if ($sessionAdapter->expireIfInactive($tempo_limite)) {
        // 🎯 DETEÇÃO DE AJAX: Verifica se a requisição veio via JavaScript (AJAX)
        $isAjax = $request->isAjax();
        
        if ($isAjax) {
            // Se for AJAX, envia o código HTTP correto de Não Autorizado e para aqui
            http_response_code(401);
            exit;
        } else {
            // Se for um clique normal no navegador, faz o redirecionamento convencional
            \Salas\Infrastructure\Http\Response::redirect(app_url('?acao=login&errormsg=' . urlencode("Sessão expirada.")))->send();
            exit;
        }
}

// 1. Intersetar Logout e Login
if ($request->action() === 'logout') {
    // 🎯 CORREÇÃO: Chama o método diretamente do controlador unificado
    $UsuarioController = new UsuarioController($database);
    $UsuarioController->logout();
    exit;
    }

if ($request->action() === 'login') {
    $loginView = (new LoginController($database))->handle($request->allQuery(), $request->allPost());
    if ($loginView['redirect'] !== null) {
        \Salas\Infrastructure\Http\Response::redirect($loginView['redirect'])->send();
        exit;
    }

    ViewRenderer::render('Login.php', ['loginView' => $loginView]);
    exit;
    }

if ($request->action() === 'imagens_list') {
    require_once $appRoot . '/../src/Controllers/SalaController.php';
    $ctrl = new \Salas\Controllers\SalaController($database);
    header('Content-Type: application/json; charset=UTF-8');
    echo $ctrl->listarImagensJson($request->allQuery());
    exit;
    }

// 2. Sistema de distribuição de páginas baseado no Menu
if ($request->action() !== null) {
    $pagina = $request->query('acao');
    $routes = include $appRoot . '/config/routes.php';

    if (array_key_exists($pagina, $routes)) {
        $page = (new Router($database))->dispatch($pagina, $request->allQuery(), $request->allPost());
        if (array_key_exists('response', $page)) {
            header('Content-Type: text/plain; charset=UTF-8');
            echo $page['response'];
            exit;
        }
        $layout = $page['data']['_layout'] ?? null;
        ViewRenderer::render($page['view'], $page['data'], $layout);
        exit;
    }

    echo "<h1>Página não encontrada no MVC!</h1>";
    exit;
}

// 3. Se nenhuma ação for passada na URL, carrega o Dashboard inicial padrão
$viewData = (new Router($database))->dashboard($request->allQuery());
ViewRenderer::render('Index.php', $viewData);

?>
