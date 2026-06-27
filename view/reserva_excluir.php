<?php

define('REQUIRED_PERMISSION', 'M_Reservas');
require_once "seguranca.php";
require_once '../controller/reservaController.php';

$reserva = new ReservaController();

// salvar dados
$reserva->excluirController();

?>