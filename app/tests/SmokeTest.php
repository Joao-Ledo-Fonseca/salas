<?php

PHP_SAPI === 'cli' or exit('CLI only');

require_once __DIR__ . '/../src/config/bootstrap.php';
require_once __DIR__ . '/../src/core/Persistence/Database.php';

$database = new \Salas\Core\Persistence\Database($appConfig);

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

// Authorization service (now in infrastructure/Auth)
try {
    require_once __DIR__ . '/../src/core/Auth/AuthorizationService.php';
    $authz = new \Salas\Core\Auth\AuthorizationService($database);
    assert_true($authz->nomeNivel(3, 2) === 'SuperAdmin', 'AuthorizationService level name failed');
    assert_true($authz->validaPermissao('M_Reservas', 3), 'SuperAdmin should have M_Reservas');
    assert_true(!$authz->validaPermissao('M_Permissoes', 0), 'External user should not have M_Permissoes');
} catch (\Throwable $e) {
    $failures[] = 'AuthorizationService exception: ' . $e->getMessage();
}

// Categoria model
try {
    require_once __DIR__ . '/../src/models/Categoria.php';
    $categorias = new \Salas\Models\Categoria($database);
    $list = $categorias->listar();
    assert_true(is_array($list), 'Categoria::listar did not return an array');
} catch (\Throwable $e) {
    $failures[] = 'Categoria exception: ' . $e->getMessage();
}

// Sala model
try {
    require_once __DIR__ . '/../src/models/Sala.php';
    $rooms = new \Salas\Models\Sala($database);
    $list = $rooms->listar();
    assert_true(is_array($list), 'Sala::listar did not return an array');
} catch (\Throwable $e) {
    $failures[] = 'Sala exception: ' . $e->getMessage();
}

// Periodo model
try {
    require_once __DIR__ . '/../src/models/Periodo.php';
    $periods = new \Salas\Models\Periodo($database);
    $list = $periods->listar();
    assert_true(is_array($list), 'Periodo::listar did not return an array');
} catch (\Throwable $e) {
    $failures[] = 'Periodo exception: ' . $e->getMessage();
}

// AuthService inlined in LoginController (authenticate via Usuario)
try {
    require_once __DIR__ . '/../src/models/Usuario.php';
    $usuario = new \Salas\Models\Usuario($database);
    $identity = $usuario->autenticar('utilizador_inexistente', 'senha_invalida');
    assert_true($identity === [] || $identity === null, 'Usuario should reject invalid credentials');
} catch (\Throwable $e) {
    $failures[] = 'Usuario exception: ' . $e->getMessage();
}


// core/ Http Request
try {
    require_once __DIR__ . '/../src/core/Http/Request.php';
    $req = new \Salas\Core\Http\Request(['acao' => 'sala_form']);
    assert_true($req->action() === 'sala_form', 'Request::action failed');
    assert_true($req->isAjax() === false, 'Request::isAjax default false failed');
} catch (\Throwable $e) {
    $failures[] = 'Request exception: ' . $e->getMessage();
}

// core/ Http Response
try {
    require_once __DIR__ . '/../src/core/Http/Response.php';
    $res = \Salas\Core\Http\Response::redirect('?acao=login');
    assert_true($res->getStatusCode() === 302, 'Response::redirect status failed');
    assert_true($res->getHeaders()['Location'] === '?acao=login', 'Response::redirect Location failed');
} catch (\Throwable $e) {
    $failures[] = 'Response exception: ' . $e->getMessage();
}

// core/ Support Util
try {
    require_once __DIR__ . '/../src/core/Support/Util.php';
    $d = \Salas\Core\Support\Util::parseDataPt('29/10/2026');
    assert_true($d instanceof \DateTime, 'Util::parseDataPt failed');
} catch (\Throwable $e) {
    $failures[] = 'Util exception: ' . $e->getMessage();
}

if ($failures === []) {
    echo "Smoke tests passed.\n";
    exit(0);
}

foreach ($failures as $failure) {
    echo "FAIL: " . $failure . "\n";
}
exit(1);
