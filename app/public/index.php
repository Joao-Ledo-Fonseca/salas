<?php

require_once __DIR__ . '/../core/views/config.php';

$projectRoot = dirname(__DIR__);
$appRoot = is_dir($projectRoot . DIRECTORY_SEPARATOR . 'app')
    ? $projectRoot
    : $projectRoot . DIRECTORY_SEPARATOR . 'core';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

$sessionAdapterPath = $appRoot . '/app/integration/SalasSessionAdapter.php';
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
    require_once $appRoot . '/app/controllers/usuarioController.php';
    $usuarioController = new usuarioController();
    $usuarioController->logout();
    exit;
    }

if (isset($_GET['acao']) && $_GET['acao'] === 'login') {
    require_once $appRoot . '/app/controllers/LoginController.php';
    $loginView = (new LoginController())->handle($_GET, $_POST);
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
    $routes = include $appRoot . '/app/config/routes.php';

    if (array_key_exists($pagina, $routes)) {
        require_once $appRoot . '/views/' . $routes[$pagina];
        exit;
    }

    echo "<h1>Página não encontrada no MVC!</h1>";
    exit;
}

// 3. Se nenhuma ação for passada na URL, carrega o Dashboard inicial padrão
require_once $appRoot . '/views/seguranca.php';
require_once $appRoot . '/app/controllers/DashboardPageController.php';
$dashboardView = (new DashboardPageController())->index($_GET);
require_once $appRoot . '/views/index.php';

?>