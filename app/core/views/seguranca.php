<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../app/integration/SalasSessionAdapter.php';
$sessionAdapter = new \Salas\Integration\SalasSessionAdapter();
$sessionAdapter->start();

//  1. Deteção central de chamadas JavaScript (AJAX)
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!$sessionAdapter->isAuthenticated()) {

    //  CORREÇÃO CRÍTICA: Se for AJAX e a sessão expirou, trava com 401
    if ($isAjax) {
        http_response_code(401);
        exit;
    } else {
        // Se for navegação normal no browser, redireciona convencionalmente
        header('Location: ' . app_url('?acao=login'));
        exit;    
    }
}

if (defined('REQUIRED_PERMISSION')) {
    
    require_once __DIR__ . "/../app/controllers/permissoesController.php";

    $pc = new permissoesController();
    if (!$pc->validaPermissao(REQUIRED_PERMISSION, $_SESSION['user_nivel'])) {
        
        // CORREÇÃO EXTRA: Se falhar a permissão via AJAX, também trava com 403 (Forbidden)
        if ($isAjax) {
            http_response_code(403); // Opcional: intercetor pode ler 403 para mandar para a Home
            exit;
        } else {
            header('Location: ' . app_url());
            exit;
        }
    }
}
?>