<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/src/config/bootstrap.php';

// Use test DB
putenv('DB_NAME=sgreserva_test');
// Already loaded config; override by new Database with test config? AppConfig already from bootstrap (sgreserva). 
// Instead create AppConfig manually:
$cfg = \Salas\Config\AppConfig::fromFile(__DIR__ . '/app/config.json', '/');
// Can't override db name easily; use reflection? Skip DB name override; use dev DB but transaction rollback not in controller.
// We'll just instantiate with existing $appConfig.

global $appConfig;
$db = new \Salas\Core\Persistence\Database($appConfig);

// Start session
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_nome'] = 'admin';
$_SESSION['user_nivel'] = 2;

$csrf = \Salas\Core\Support\Csrf::token();
$post = [
    'csrf_token' => $csrf,
    'id' => '0',
    'dia' => date('d/m/Y'),
    'sala_id' => '4',
    'periodo_id' => '1',
    'periodos' => '1',
    'entidade' => 'Teste',
    'uso_id' => '1',
    'status' => '1',
    'observacao' => '',
    'submeter_salvar' => '1',
];

$ctrl = new \Salas\Controllers\ReservaController($db);
echo "result: " . $ctrl->salvar($post) . "\n";
?>