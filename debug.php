<?php
// debug.php - Debug relatorios_teste route
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'app/src/Config/bootstrap.php';

$db = new Salas\Core\Persistence\Database($GLOBALS['appConfig']);
$r = new Salas\Core\Router($db);

try {
    echo "=== Testing relatorios_teste route ===" . PHP_EOL;
    $result = $r->dispatch('relatorios_teste', [], []);
    echo "view type: " . gettype($result['view']) . PHP_EOL;
    echo "view value: " . var_export($result['view'], true) . PHP_EOL;
    echo "data keys: " . implode(', ', array_keys($result['data'])) . PHP_EOL;
    echo PHP_EOL;
    
    // Check routes.php
    $routes = include 'app/src/Config/routes.php';
    echo "routes.php relatorios_teste: " . PHP_EOL;
    print_r($routes['relatorios_teste']);
    echo PHP_EOL;
    
    // Check if _view exists
    $route = $routes['relatorios_teste'];
    echo "Has _view: " . (isset($route['_view']) ? 'YES - ' . $route['_view'] : 'NO') . PHP_EOL;
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}