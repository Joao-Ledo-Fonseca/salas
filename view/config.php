<?php

$cfg = new stdClass();

// configuração de banco de dados
$cfg->db_host = '127.0.0.1';
$cfg->db_user = 'sgreserva';
$cfg->db_senha = 'sgreserva';
$cfg->db_banco = 'sgreserva';
$cfg->db_porta = 3306;

// cores do dashboard
$cfg->dashboard_colors = new stdClass();
$cfg->dashboard_colors->reservado = '#FBFABB';
$cfg->dashboard_colors->confirmado = '#BFF9B9';
$cfg->dashboard_colors->cancelado = '#FCC5C5';
$cfg->dashboard_colors->disponivel = '#FFFFFF';
$cfg->dashboard_colors->disponivel_hover = '#DFDFDF';
$cfg->dashboard_colors->selected = '#BFF9B9';
$cfg->dashboard_colors->cell_border = '#E6E6E6';
$cfg->dashboard_colors->cell_text = '#333333';

setlocale(LC_TIME, 'portuguese');
date_default_timezone_set("Europe/Lisbon");

?>