<?php


require_once __DIR__ . '/../src/config/bootstrap.php';

$projectRoot = dirname(__DIR__);
$appRoot = $projectRoot . DIRECTORY_SEPARATOR . 'src';

require_once $appRoot . '/models/db_pdo.php';
$database = new Database($appConfig);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

$sessionAdapterPath = $appRoot . '/integration/SalasSessionAdapter.php';
require_once $sessionAdapterPath;
$sessionAdapter = new \Salas\Integration\SalasSessionAdapter();
$sessionAdapter->start();

// 🎯 CONFIGURAÇÃO: Tempo limite de inatividade (ex: 1800s = 30 minutos)
$tempo_limite = 600; 

if ($sessionAdapter->expireIfInactive($tempo_limite)) {
        // 🎯 DETEÇÃO DE AJAX: Verifica se a requisição veio via JavaScript (AJAX)
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($isAjax) {
            // Se for AJAX, envia o código HTTP correto de Não Autorizado e para aqui
            http_response_code(401);
            exit;
        } else {
            // Se for um clique normal no navegador, faz o redirecionamento convencional
            header("Location: " . app_url('?acao=login&errormsg=' . urlencode("Sessão expirada.")));
            exit;
        }
}

// 1. Intersetar Logout e Login
if (isset($_GET['acao']) && $_GET['acao'] === 'logout') {
    // 🎯 CORREÇÃO: Chama o método diretamente do controlador unificado
    require_once $appRoot . '/controllers/usuarioController.php';
    $usuarioController = new usuarioController($database);
    $usuarioController->logout();
    exit;
    }

if (isset($_GET['acao']) && $_GET['acao'] === 'login') {
    require_once $appRoot . '/controllers/LoginController.php';
    $loginView = (new LoginController($database))->handle($_GET, $_POST);
    if ($loginView['redirect'] !== null) {
        header('Location: ' . $loginView['redirect']);
        exit;
    }

    require_once $appRoot . '/views/login.php';
    exit;
    }

// 2. Sistema de distribuição de páginas baseado no Menu
if (isset($_GET['acao'])) {
    $pagina = $_GET['acao'];
    $routes = include $appRoot . '/config/routes.php';

    if (array_key_exists($pagina, $routes)) {
        require_once $appRoot . '/controllers/ApplicationPageController.php';
        $page = (new ApplicationPageController($database))->dispatch($pagina, $_GET, $_POST);
        if (array_key_exists('response', $page)) {
            header('Content-Type: text/plain; charset=UTF-8');
            echo $page['response'];
            exit;
        }
        extract($page['data'], EXTR_SKIP);
        require_once $appRoot . '/views/' . $page['view'];
        exit;
    }

    echo "<h1>Página não encontrada no MVC!</h1>";
    exit;
}

// 3. Se nenhuma ação for passada na URL, carrega o Dashboard inicial padrão
require_once $appRoot . '/controllers/ApplicationPageController.php';
$viewData = (new ApplicationPageController($database))->dashboard($_GET);
extract($viewData, EXTR_SKIP);
require_once $appRoot . '/views/index.php';

?>
