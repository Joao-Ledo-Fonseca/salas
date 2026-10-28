<?php

PHP_SAPI === 'cli' or exit('CLI only');

require_once __DIR__ . '/../src/config/bootstrap.php';
require_once __DIR__ . '/../src/infrastructure/Persistence/Database.php';

$database = new Database($appConfig);

$failures = [];

function assert_true(bool $condition, string $message): void
{
    global $failures;
    if (!$condition) {
        $failures[] = $message;
    }
}

// Database
try {
    $result = $database->query('SELECT 1 AS ok');
    assert_true(isset($result[0]['ok']) && (int) $result[0]['ok'] === 1, 'Database connection failed');
} catch (\Throwable $e) {
    $failures[] = 'Database exception: ' . $e->getMessage();
}

// Authorization service
try {
    require_once __DIR__ . '/../src/application/Services/AuthorizationService.php';
    $authz = new AuthorizationService($database);
    assert_true($authz->nomeNivel(3, 2) === 'SuperAdmin', 'AuthorizationService level name failed');
    assert_true($authz->validaPermissao('M_Reservas', 3), 'SuperAdmin should have M_Reservas');
    assert_true(!$authz->validaPermissao('M_Permissoes', 0), 'External user should not have M_Permissoes');
} catch (\Throwable $e) {
    $failures[] = 'AuthorizationService exception: ' . $e->getMessage();
}

// Category service
try {
    require_once __DIR__ . '/../src/application/Services/CategoryService.php';
    $categories = new CategoryService($database);
    $list = $categories->list();
    assert_true(is_array($list), 'CategoryService::list did not return an array');
} catch (\Throwable $e) {
    $failures[] = 'CategoryService exception: ' . $e->getMessage();
}

// Sala service
try {
    require_once __DIR__ . '/../src/application/Services/SalaService.php';
    $rooms = new SalaService($database);
    $list = $rooms->list();
    assert_true(is_array($list), 'SalaService::list did not return an array');
} catch (\Throwable $e) {
    $failures[] = 'SalaService exception: ' . $e->getMessage();
}

// Periodo service
try {
    require_once __DIR__ . '/../src/application/Services/PeriodoService.php';
    $periods = new PeriodoService($database);
    $list = $periods->list();
    assert_true(is_array($list), 'PeriodoService::list did not return an array');
} catch (\Throwable $e) {
    $failures[] = 'PeriodoService exception: ' . $e->getMessage();
}
try {
    require_once __DIR__ . '/../src/application/Services/AuthService.php';
    $auth = new AuthService($database);
    $identity = $auth->authenticate('utilizador_inexistente', 'senha_invalida');
    assert_true($identity === null, 'AuthService should reject invalid credentials');
} catch (\Throwable $e) {
    $failures[] = 'AuthService exception: ' . $e->getMessage();
}

if ($failures === []) {
    echo "Smoke tests passed.\n";
    exit(0);
}

foreach ($failures as $failure) {
    echo "FAIL: " . $failure . "\n";
}
exit(1);
