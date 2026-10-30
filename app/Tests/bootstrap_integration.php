<?php
putenv('DB_NAME=sgreserva_test');
$_ENV['DB_NAME'] = 'sgreserva_test';
require __DIR__ . '/../src/Config/bootstrap.php';
$GLOBALS['appConfig'] = $appConfig;
?>