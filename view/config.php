<?php

$cfg = new stdClass();

// configuração de banco de dados
$cfg->db_host = '127.0.0.1';
$cfg->db_user = 'sgreserva';
$cfg->db_senha = 'sgreserva';
$cfg->db_banco = 'sgreserva';
$cfg->db_porta = 3306;

setlocale(LC_TIME, 'portuguese');
date_default_timezone_set("Europe/Lisbon");

?>