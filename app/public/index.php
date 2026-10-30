<?php



$projectRoot = dirname(__DIR__);
$appRoot = $projectRoot . DIRECTORY_SEPARATOR . 'src';

require_once $appRoot . '/../src/Config/bootstrap.php';

use Salas\Core\Persistence\Database;
use Salas\Models\Usuario;
use Salas\Core\Http\Request;
use Salas\Core\Http\Response;
use Salas\Core\Http\ErrorHandler;
use Salas\Core\Http\ViewRenderer;
use Salas\Core\Auth\SalasSessionAdapter;
use Salas\Controllers\LoginController;
use Salas\Controllers\UsuarioController;
use Salas\Core\Router;

$database = new Database($appConfig);

Usuario::seedSuperAdmin($database);

ErrorHandler::register();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

$sessionAdapter = new SalasSessionAdapter();
$sessionAdapter->start();

$request = Request::fromGlobals();

//  CONFIGURAÇÃO: Tempo limite de inatividade (ex: 1800s = 30 minutos)
$tempo_limite = 600; 

if ($sessionAdapter->expireIfInactive($tempo_limite)) {
        //  DETEÇÃO DE AJAX: Verifica se a requisição veio via JavaScript (AJAX)
        $isAjax = $request->isAjax();
        
        if ($isAjax) {
            // Se for AJAX, envia o código HTTP correto de Não Autorizado e para aqui
            http_response_code(401);
            exit;
        } else {
            // Se for um clique normal no navegador, faz o redirecionamento convencional
            Response::redirect(app_url('?acao=login&errormsg=' . urlencode("Sessão expirada.")))->send();
            exit;
        }
}

// 1. Intersetar Logout e Login
if ($request->action() === 'logout') {
    (new Router($database))->logout();
    exit;
}

if ($request->action() === 'login') {
    (new Router($database))->login($request);
    exit;
}

if ($request->action() === 'imagens_list') {
    (new Router($database))->imagensList($request);
    exit;
}










// 2. Sistema de distribuição de páginas baseado no Menu
if ($request->action() !== null) {
    $pagina = $request->query('acao');
    $routes = include $appRoot . '/Config/routes.php';

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
