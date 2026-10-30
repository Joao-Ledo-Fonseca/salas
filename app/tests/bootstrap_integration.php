<?php
putenv('DB_NAME=sgreserva_test');
$_ENV['DB_NAME'] = 'sgreserva_test';
require __DIR__ . '/../src/config/bootstrap.php';
$GLOBALS['appConfig'] = $appConfig;
?>